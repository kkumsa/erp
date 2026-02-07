<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // expenses: '대기' → '승인요청' 추가
        DB::statement("ALTER TABLE expenses MODIFY COLUMN status ENUM('대기', '승인요청', '승인', '반려', '결제완료') DEFAULT '대기'");

        // leaves: '대기' → '승인요청' 추가
        DB::statement("ALTER TABLE leaves MODIFY COLUMN status ENUM('대기', '승인요청', '승인', '반려', '취소') DEFAULT '대기'");

        // timesheets: '대기' → '승인요청' 추가
        DB::statement("ALTER TABLE timesheets MODIFY COLUMN status ENUM('대기', '승인요청', '승인', '반려') DEFAULT '대기'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE expenses MODIFY COLUMN status ENUM('대기', '승인', '반려', '결제완료') DEFAULT '대기'");
        DB::statement("ALTER TABLE leaves MODIFY COLUMN status ENUM('대기', '승인', '반려', '취소') DEFAULT '대기'");
        DB::statement("ALTER TABLE timesheets MODIFY COLUMN status ENUM('대기', '승인', '반려') DEFAULT '대기'");
    }
};
