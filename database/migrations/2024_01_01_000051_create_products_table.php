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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // SKU
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('unit')->default('개'); // 단위
            $table->decimal('purchase_price', 15, 2)->nullable(); // 매입가
            $table->decimal('selling_price', 15, 2)->nullable(); // 판매가
            $table->integer('min_stock')->default(0); // 최소 재고
            $table->integer('max_stock')->nullable(); // 최대 재고
            $table->boolean('is_active')->default(true);
            $table->boolean('is_stockable')->default(true); // 재고 관리 여부
            $table->string('barcode')->nullable();
            $table->string('image_path')->nullable();
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
        Schema::dropIfExists('products');
    }
};
