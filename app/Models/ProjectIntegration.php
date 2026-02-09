<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectIntegration extends Model
{
    protected $fillable = [
        'project_id',
        'provider',
        'config',
        'sync_direction',
        'is_active',
        'last_synced_at',
        'last_sync_error',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public const SYNC_JIRA_TO_ERP = 'jira_to_erp';
    public const SYNC_ERP_TO_JIRA = 'erp_to_jira';
    public const SYNC_BIDIRECTIONAL = 'bidirectional';

    public const PROVIDER_JIRA = 'jira';

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function canPullFromExternal(): bool
    {
        return in_array($this->sync_direction, [self::SYNC_JIRA_TO_ERP, self::SYNC_BIDIRECTIONAL], true);
    }

    public function canPushToExternal(): bool
    {
        return in_array($this->sync_direction, [self::SYNC_ERP_TO_JIRA, self::SYNC_BIDIRECTIONAL], true);
    }

    public function getConfig(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }
}
