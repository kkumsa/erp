<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 프로젝트 코드
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete(); // PM
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->decimal('budget', 15, 2)->nullable(); // 예산
            $table->decimal('actual_cost', 15, 2)->default(0); // 실비용
            $table->enum('status', ['계획중', '진행중', '보류', '완료', '취소'])->default('계획중');
            $table->integer('progress')->default(0); // 진행률 (%)
            $table->enum('priority', ['낮음', '보통', '높음', '긴급'])->default('보통');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 프로젝트 멤버 테이블
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('멤버'); // PM, 개발자, 디자이너 등
            $table->date('joined_at');
            $table->date('left_at')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('projects');
    }
};
