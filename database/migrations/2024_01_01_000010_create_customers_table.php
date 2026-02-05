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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('business_number')->nullable()->unique(); // 사업자번호
            $table->string('representative')->nullable(); // 대표자
            $table->string('industry')->nullable(); // 업종
            $table->string('business_type')->nullable(); // 업태
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->enum('type', ['잠재고객', '고객', 'VIP', '휴면'])->default('잠재고객');
            $table->enum('status', ['활성', '비활성'])->default('활성');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // 담당자
            $table->text('note')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
