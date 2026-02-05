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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique(); // 계약번호
            $table->string('title'); // 계약명
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opportunity_id')->nullable()->constrained()->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('amount', 15, 2); // 계약 금액
            $table->enum('status', ['초안', '검토중', '서명완료', '진행중', '완료', '해지'])->default('초안');
            $table->enum('payment_terms', ['선불', '후불', '분할'])->default('후불');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable(); // 계약서 파일
            $table->foreignId('signed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
