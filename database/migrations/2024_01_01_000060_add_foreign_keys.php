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
        // 청구서에 프로젝트 연결
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
        });

        // 비용에 프로젝트, 공급업체 연결
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
        });

        // 발주서에 프로젝트 연결
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
        });

        // 청구서 항목에 상품 연결
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        // 발주서 항목에 상품 연결
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['supplier_id']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
    }
};
