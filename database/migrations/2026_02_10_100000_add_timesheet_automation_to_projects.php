<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('timesheet_automation_enabled')->default(false)->after('meta')->comment('타임시트 자동화(할당 시 알림/초안 생성)');
            $table->boolean('timesheet_integration_enabled')->default(false)->after('timesheet_automation_enabled')->comment('타임시트 외부 연동(Toggl 등) 사용 여부');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['timesheet_automation_enabled', 'timesheet_integration_enabled']);
        });
    }
};
