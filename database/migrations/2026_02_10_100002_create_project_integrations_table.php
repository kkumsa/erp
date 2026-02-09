<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 50)->comment('jira, linear 등');
            $table->json('config')->comment('base_url, project_key, api_token 등');
            $table->string('sync_direction', 30)->default('jira_to_erp')->comment('jira_to_erp, erp_to_jira, bidirectional');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_sync_error')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_integrations');
    }
};
