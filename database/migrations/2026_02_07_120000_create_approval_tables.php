<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 결재라인 템플릿
        Schema::create('approval_flows', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // 결재라인 이름
            $table->string('target_type');                   // 대상 모델 (App\Models\PurchaseOrder 등)
            $table->json('conditions')->nullable();          // 자동 적용 조건 (금액 범위 등)
            $table->boolean('is_default')->default(false);   // 기본 결재라인 여부
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 결재 단계 정의
        Schema::create('approval_flow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_flow_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('step_order');      // 단계 순서 (1, 2, 3...)
            $table->string('approver_type');                 // user, role, department_head
            $table->unsignedBigInteger('approver_id')->nullable(); // user ID 또는 role ID
            $table->string('action_type')->default('승인');   // 승인, 합의, 참조
            $table->timestamps();
        });

        // 실제 결재 요청 인스턴스
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->morphs('approvable');                    // approvable_type, approvable_id
            $table->foreignId('approval_flow_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('current_step')->default(1);
            $table->unsignedSmallInteger('total_steps');
            $table->string('status')->default('진행중');       // 진행중, 승인, 반려, 취소
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 각 단계별 결재 기록
        Schema::create('approval_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_request_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('step_order');
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');                        // 승인, 반려, 참조확인
            $table->text('comment')->nullable();
            $table->timestamp('acted_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_actions');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_flow_steps');
        Schema::dropIfExists('approval_flows');
    }
};
