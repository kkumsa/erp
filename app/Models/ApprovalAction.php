<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalAction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'approval_request_id',
        'step_order',
        'approver_id',
        'action',
        'comment',
        'acted_at',
    ];

    protected function casts(): array
    {
        return [
            'acted_at' => 'datetime',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(ApprovalRequest::class, 'approval_request_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * 액션 한글 라벨 (배지 색상용)
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            '승인' => 'success',
            '반려' => 'danger',
            '참조확인' => 'info',
            default => 'gray',
        };
    }
}
