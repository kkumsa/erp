<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->foreignId('sales_account_id')
                ->nullable()
                ->after('parent_id')
                ->constrained('accounts')
                ->nullOnDelete();

            $table->foreignId('purchase_account_id')
                ->nullable()
                ->after('sales_account_id')
                ->constrained('accounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sales_account_id');
            $table->dropConstrainedForeignId('purchase_account_id');
        });
    }
};
