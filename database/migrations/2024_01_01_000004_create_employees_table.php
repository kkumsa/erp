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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_code')->unique();
            $table->string('position')->nullable(); // 직책
            $table->string('job_title')->nullable(); // 직급
            $table->date('hire_date');
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('address')->nullable();
            $table->enum('employment_type', ['정규직', '계약직', '인턴', '파트타임'])->default('정규직');
            $table->enum('status', ['재직', '휴직', '퇴직'])->default('재직');
            $table->date('resignation_date')->nullable();
            $table->decimal('base_salary', 15, 2)->nullable();
            $table->integer('annual_leave_days')->default(15);
            $table->json('meta')->nullable(); // 추가 정보
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
