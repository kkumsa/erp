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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 연차, 병가, 경조사 등
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('default_days')->default(0); // 기본 부여 일수
            $table->boolean('is_paid')->default(true); // 유급/무급
            $table->boolean('is_active')->default(true);
            $table->string('color')->default('#3B82F6'); // UI 표시 색상
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
