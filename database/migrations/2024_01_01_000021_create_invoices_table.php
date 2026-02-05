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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable();
            $table->date('issue_date'); // 발행일
            $table->date('due_date'); // 납부기한
            $table->decimal('subtotal', 15, 2)->default(0); // 공급가액
            $table->decimal('tax_amount', 15, 2)->default(0); // 세액
            $table->decimal('total_amount', 15, 2)->default(0); // 합계
            $table->decimal('paid_amount', 15, 2)->default(0); // 결제 금액
            $table->enum('status', ['초안', '발행', '부분결제', '결제완료', '연체', '취소'])->default('초안');
            $table->text('note')->nullable();
            $table->text('terms')->nullable(); // 결제 조건
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
