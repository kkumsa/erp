<x-filament-panels::page>
    {{-- 프로필 정보 폼 --}}
    <form wire:submit="saveProfile">
        {{ $this->profileForm }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit">
                프로필 저장
            </x-filament::button>
        </div>
    </form>

    {{-- 비밀번호 변경 폼 --}}
    <form wire:submit="savePassword" class="mt-8">
        {{ $this->passwordForm }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" color="warning">
                비밀번호 변경
            </x-filament::button>
        </div>
    </form>

    {{-- 로그인 이력 --}}
    <div class="mt-8">
        <x-filament::section>
            <x-slot name="heading">로그인 이력</x-slot>
            <x-slot name="description">최근 로그인/로그아웃 기록입니다.</x-slot>

            {{ $this->table }}
        </x-filament::section>
    </div>

    {{-- 변경 이력 --}}
    <div class="mt-8">
        <x-activity-log-panel
            :subject-type="'App\Models\User'"
            :subject-id="auth()->id()"
        />
    </div>
</x-filament-panels::page>
