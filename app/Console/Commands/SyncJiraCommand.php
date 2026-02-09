<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\JiraIntegrationService;
use Illuminate\Console\Command;

class SyncJiraCommand extends Command
{
    protected $signature = 'erp:sync-jira {project_id : 프로젝트 ID}';

    protected $description = '프로젝트의 JIRA 연동을 실행합니다 (JIRA → ERP 풀)';

    public function handle(JiraIntegrationService $jira): int
    {
        $projectId = (int) $this->argument('project_id');
        $project = Project::find($projectId);

        if (!$project) {
            $this->error("프로젝트 ID {$projectId}를 찾을 수 없습니다.");
            return self::FAILURE;
        }

        $integration = $project->jiraIntegration;
        if (!$integration || !$integration->is_active) {
            $this->error('이 프로젝트에 활성 JIRA 연동이 없습니다.');
            return self::FAILURE;
        }

        if (!$integration->canPullFromExternal()) {
            $this->warn('이 연동은 JIRA→ERP 풀을 지원하지 않습니다. 동기화 방향을 확인하세요.');
            return self::FAILURE;
        }

        $this->info('JIRA에서 이슈를 가져오는 중...');
        $result = $jira->pullFromJira($integration);

        if (!empty($result['error'])) {
            $this->error('동기화 실패: ' . $result['error']);
            return self::FAILURE;
        }

        $this->info("동기화 완료: 생성 {$result['created']}건, 갱신 {$result['updated']}건");
        return self::SUCCESS;
    }
}
