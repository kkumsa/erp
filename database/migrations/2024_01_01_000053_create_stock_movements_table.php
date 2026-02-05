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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique(); // 이동 번호
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['입고', '출고', '조정', '이동', '반품']);
            $table->decimal('quantity', 15, 2); // 수량 (양수: 입고, 음수: 출고)
            $table->decimal('before_quantity', 15, 2); // 이동 전 수량
            $table->decimal('after_quantity', 15, 2); // 이동 후 수량
            $table->decimal('unit_cost', 15, 2)->nullable(); // 단가
            $table->nullableMorphs('reference'); // 연결된 문서 (발주서, 청구서 등)
            $table->foreignId('destination_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete(); // 이동 대상 창고
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
