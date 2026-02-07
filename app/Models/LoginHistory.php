<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'event',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 이벤트 한글 라벨
     */
    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'login' => '로그인',
            'logout' => '로그아웃',
            'login_failed' => '로그인 실패',
            default => $this->event,
        };
    }
}
