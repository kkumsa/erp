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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 영업 기회명
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2)->nullable(); // 예상 금액
            $table->enum('stage', ['발굴', '접촉', '제안', '협상', '계약완료', '실패'])->default('발굴');
            $table->integer('probability')->default(10); // 성공 확률 (%)
            $table->date('expected_close_date')->nullable(); // 예상 계약일
            $table->date('actual_close_date')->nullable(); // 실제 계약일
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('next_step')->nullable(); // 다음 단계
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
