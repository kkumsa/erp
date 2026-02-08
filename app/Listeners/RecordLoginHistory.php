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

        // 로그인 화면에서 선택한 언어를 사용자 설정에 반영 (영문 로그인 → 영문, 한글 로그인 → 한글)
        $locale = session('locale');
        if ($locale && in_array($locale, ['ko', 'en']) && in_array('locale', $event->user->getFillable())) {
            $event->user->update(['locale' => $locale]);
        }
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
