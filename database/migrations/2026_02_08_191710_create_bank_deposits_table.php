<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_deposits', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deposited_at')->comment('입금 일시');
            $table->string('depositor_name')->comment('입금자명');
            $table->decimal('amount', 15, 2)->comment('입금 금액');
            $table->string('transaction_number')->nullable()->comment('거래번호');
            $table->string('bank_account')->nullable()->comment('입금 회사계좌');
            $table->text('memo')->nullable()->comment('메모');
            $table->dateTime('processed_at')->nullable()->comment('처리 완료 시각');
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete()->comment('매칭된 결제');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('등록자');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_deposits');
    }
};
