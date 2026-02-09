<?php

namespace App\Services;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\ProjectIntegration;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class JiraIntegrationService
{
    /**
     * JIRA → ERP: JIRA 이슈를 가져와서 ERP 태스크로 생성/갱신
     */
    public function pullFromJira(ProjectIntegration $integration): array
    {
        $project = $integration->project;
        $config = $integration->config;
        $baseUrl = rtrim($config['base_url'] ?? '', '/');
        $projectKey = $config['project_key'] ?? '';
        $email = $config['email'] ?? '';
        $apiToken = $config['api_token'] ?? '';

        if (!$baseUrl || !$projectKey || !$email || !$apiToken) {
            $integration->update(['last_sync_error' => '설정 부족: base_url, project_key, email, api_token 필요']);
            return ['created' => 0, 'updated' => 0, 'error' => 'Invalid config'];
        }

        $url = $baseUrl . '/rest/api/3/search';
        $jql = 'project = ' . $projectKey . ' ORDER BY created ASC';
        $response = Http::withBasicAuth($email, $apiToken)
            ->accept('application/json')
            ->get($url, [
                'jql' => $jql,
                'maxResults' => 100,
                'fields' => 'summary,description,status,assignee,duedate,created,updated,priority',
            ]);

        if (!$response->successful()) {
            $err = $response->body();
            $integration->update(['last_sync_error' => $err, 'last_synced_at' => now()]);
            return ['created' => 0, 'updated' => 0, 'error' => $err];
        }

        $data = $response->json();
        $issues = $data['issues'] ?? [];
        $created = 0;
        $updated = 0;

        foreach ($issues as $issue) {
            $key = $issue['key'];
            $fields = $issue['fields'] ?? [];
            $existing = Task::withoutGlobalScopes()
                ->where('project_id', $project->id)
                ->where('external_source', 'jira')
                ->where('external_id', $key)
                ->first();

            $assigneeId = null;
            if (!empty($fields['assignee']['emailAddress'])) {
                $user = User::where('email', $fields['assignee']['emailAddress'])->first();
                $assigneeId = $user?->id;
            }

            $status = $this->mapJiraStatusToErp($fields['status']['name'] ?? '');
            $priority = $this->mapJiraPriorityToErp($fields['priority']['name'] ?? '');

            $payload = [
                'title' => $fields['summary'] ?? $key,
                'description' => is_array($fields['description'] ?? null)
                    ? (isset($fields['description']['content']) ? $this->jiraContentToPlain($fields['description']) : null)
                    : ($fields['description'] ?? null),
                'assigned_to' => $assigneeId,
                'status' => $status,
                'priority' => $priority,
                'due_date' => !empty($fields['duedate']) ? $fields['duedate'] : null,
                'external_id' => $key,
                'external_source' => 'jira',
                'external_data' => [
                    'jira_key' => $key,
                    'jira_status' => $fields['status']['name'] ?? null,
                    'jira_updated' => $fields['updated'] ?? null,
                ],
            ];

            if ($existing) {
                $existing->update($payload);
                $updated++;
            } else {
                Task::create(array_merge($payload, [
                    'project_id' => $project->id,
                    'created_by' => $project->manager_id ?? auth()->id(),
                ]));
                $created++;
            }
        }

        $integration->update([
            'last_synced_at' => now(),
            'last_sync_error' => null,
        ]);

        return ['created' => $created, 'updated' => $updated, 'error' => null];
    }

    /**
     * ERP → JIRA: ERP 태스크를 JIRA 이슈로 생성/갱신
     */
    public function pushToJira(Task $task): array
    {
        $project = $task->project;
        $integration = $project->integrations()->where('provider', 'jira')->where('is_active', true)->first();
        if (!$integration || !$integration->canPushToExternal()) {
            return ['success' => false, 'error' => 'No active JIRA push integration for this project'];
        }

        $config = $integration->config;
        $baseUrl = rtrim($config['base_url'] ?? '', '/');
        $projectKey = $config['project_key'] ?? '';
        $email = $config['email'] ?? '';
        $apiToken = $config['api_token'] ?? '';
        $issueTypeId = $config['issue_type_id'] ?? '10001'; // JIRA 기본 "Task" 타입 ID

        if (!$baseUrl || !$projectKey || !$email || !$apiToken) {
            return ['success' => false, 'error' => 'Invalid JIRA config'];
        }

        if ($task->external_source === 'jira' && $task->external_id) {
            // 기존 이슈 업데이트
            $url = $baseUrl . '/rest/api/3/issue/' . $task->external_id;
            $body = [
                'fields' => [
                    'summary' => $task->title,
                    'description' => $this->plainToJiraContent($task->description ?? ''),
                    'duedate' => $task->due_date?->format('Y-m-d'),
                ],
            ];
            $response = Http::withBasicAuth($email, $apiToken)
                ->accept('application/json')
                ->put($url, $body);
        } else {
            // 새 이슈 생성
            $url = $baseUrl . '/rest/api/3/issue';
            $body = [
                'fields' => [
                    'project' => ['key' => $projectKey],
                    'summary' => $task->title,
                    'description' => $this->plainToJiraContent($task->description ?? ''),
                    'issuetype' => ['id' => $issueTypeId],
                    'duedate' => $task->due_date?->format('Y-m-d'),
                ],
            ];
            if ($task->assignee?.email) {
                $body['fields']['assignee'] = ['accountId' => null]; // JIRA Cloud는 accountId 사용, 이메일로 찾으려면 사용자 검색 API 필요
            }
            $response = Http::withBasicAuth($email, $apiToken)
                ->accept('application/json')
                ->post($url, $body);
        }

        if (!$response->successful()) {
            return ['success' => false, 'error' => $response->body()];
        }

        if (!$task->external_id) {
            $key = $response->json('key');
            $task->updateQuietly([
                'external_id' => $key,
                'external_source' => 'jira',
                'external_data' => array_merge($task->external_data ?? [], ['jira_key' => $key]),
            ]);
        }

        return ['success' => true, 'key' => $task->external_id ?? $response->json('key')];
    }

    private function mapJiraStatusToErp(string $jiraStatus): string
    {
        $s = strtolower($jiraStatus);
        if (str_contains($s, 'done') || str_contains($s, 'complete')) {
            return TaskStatus::Completed->value;
        }
        if (str_contains($s, 'progress') || str_contains($s, 'in progress')) {
            return TaskStatus::InProgress->value;
        }
        if (str_contains($s, 'review')) {
            return TaskStatus::InReview->value;
        }
        if (str_contains($s, 'hold') || str_contains($s, 'wait')) {
            return TaskStatus::OnHold->value;
        }
        return TaskStatus::Pending->value;
    }

    private function mapJiraPriorityToErp(string $jiraPriority): string
    {
        $p = strtolower($jiraPriority);
        if (str_contains($p, 'highest') || str_contains($p, 'high')) {
            return Priority::High->value;
        }
        if (str_contains($p, 'low')) {
            return Priority::Low->value;
        }
        if (str_contains($p, 'critical') || str_contains($p, 'urgent')) {
            return Priority::Urgent->value;
        }
        return Priority::Normal->value;
    }

    private function jiraContentToPlain(array $desc): string
    {
        if (!isset($desc['content'])) {
            return '';
        }
        $parts = [];
        foreach ($desc['content'] as $block) {
            if (($block['type'] ?? '') === 'paragraph' && isset($block['content'])) {
                foreach ($block['content'] as $inline) {
                    if (($inline['type'] ?? '') === 'text') {
                        $parts[] = $inline['text'] ?? '';
                    }
                }
            }
        }
        return implode("\n", $parts);
    }

    private function plainToJiraContent(string $text): array
    {
        $lines = array_filter(explode("\n", $text));
        $content = [];
        foreach ($lines as $line) {
            $content[] = [
                'type' => 'paragraph',
                'content' => [['type' => 'text', 'text' => $line]],
            ];
        }
        if (empty($content)) {
            $content[] = ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $text ?: ' ']]];
        }
        return ['type' => 'doc', 'version' => 1, 'content' => $content];
    }
}
