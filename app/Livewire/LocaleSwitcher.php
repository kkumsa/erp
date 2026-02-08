<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Livewire\Component;

class LocaleSwitcher extends Component
{
    public string $locale;

    public function mount(): void
    {
        $this->locale = auth()->user()?->locale ?? App::getLocale();
    }

    public function switchLocale(string $locale): void
    {
        if (!in_array($locale, ['ko', 'en'])) {
            return;
        }

        $user = auth()->user();
        if ($user) {
            $user->update(['locale' => $locale]);
        }

        $this->locale = $locale;

        // 페이지 새로고침으로 전체 UI 반영
        $this->redirect(request()->header('Referer', '/admin'));
    }

    public function render()
    {
        return view('livewire.locale-switcher');
    }
}
