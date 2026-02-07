<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // enum에 '승인요청' 추가, 기존 '승인대기'→ 통합, '발주'도 추가
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('초안', '승인대기', '승인요청', '승인', '발주완료', '발주', '부분입고', '입고완료', '입고중', '완료', '취소') DEFAULT '초안'");

        // 기존 '승인대기' → '승인요청'으로 마이그레이션
        DB::table('purchase_orders')->where('status', '승인대기')->update(['status' => '승인요청']);
    }

    public function down(): void
    {
        DB::table('purchase_orders')->where('status', '승인요청')->update(['status' => '승인대기']);
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('초안', '승인대기', '승인', '발주완료', '부분입고', '입고완료', '취소') DEFAULT '초안'");
    }
};
