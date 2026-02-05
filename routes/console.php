<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 매일 연체 청구서 체크
Schedule::call(function () {
    \App\Models\Invoice::where('status', '발행')
        ->where('due_date', '<', now())
        ->update(['status' => '연체']);
})->daily();

// 매일 자정에 프로젝트 진행률 자동 계산
Schedule::call(function () {
    \App\Models\Project::where('status', '진행중')->each(function ($project) {
        $project->update(['progress' => $project->calculateProgress()]);
    });
})->daily();
