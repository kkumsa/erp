<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('external_id', 100)->nullable()->after('sort_order')->comment('외부 이슈 키 예: PROJ-123');
            $table->string('external_source', 50)->nullable()->after('external_id')->comment('jira, linear 등');
            $table->json('external_data')->nullable()->after('external_source')->comment('외부 원본 메타');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['external_id', 'external_source', 'external_data']);
        });
    }
};
