<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

class RecordLoginHistory
{
    public function handleLogin(Login $event): void
    {
        $this->record($event->user, 'login');
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            $this->record($event->user, 'logout');
        }
    }

    public function handleFailed(Failed $event): void
    {
        // 실패 시에도 user가 있으면 기록 (존재하는 계정에 대한 실패)
        if ($event->user) {
            $this->record($event->user, 'login_failed');
        }
    }

    private function record($user, string $event): void
    {
        LoginHistory::create([
            'user_id' => $user->id,
            'event' => $event,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
