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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('code')->unique(); // 공급업체 코드
            $table->string('business_number')->nullable(); // 사업자번호
            $table->string('representative')->nullable(); // 대표자
            $table->string('contact_name')->nullable(); // 담당자
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('bank_name')->nullable(); // 은행명
            $table->string('bank_account')->nullable(); // 계좌번호
            $table->string('bank_holder')->nullable(); // 예금주
            $table->enum('status', ['활성', '비활성'])->default('활성');
            $table->enum('payment_terms', ['선불', '후불', '정산'])->default('후불');
            $table->integer('payment_days')->default(30); // 결제 기한 (일)
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
