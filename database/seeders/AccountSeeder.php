<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // 자산
            ['code' => '1000', 'name' => '자산', 'type' => '자산', 'sort_order' => 1],
            ['code' => '1100', 'name' => '유동자산', 'type' => '자산', 'sort_order' => 2],
            ['code' => '1110', 'name' => '현금', 'type' => '자산', 'sort_order' => 3],
            ['code' => '1120', 'name' => '보통예금', 'type' => '자산', 'sort_order' => 4],
            ['code' => '1130', 'name' => '매출채권', 'type' => '자산', 'sort_order' => 5],
            ['code' => '1200', 'name' => '비유동자산', 'type' => '자산', 'sort_order' => 6],
            ['code' => '1210', 'name' => '비품', 'type' => '자산', 'sort_order' => 7],
            ['code' => '1220', 'name' => '차량운반구', 'type' => '자산', 'sort_order' => 8],

            // 부채
            ['code' => '2000', 'name' => '부채', 'type' => '부채', 'sort_order' => 20],
            ['code' => '2100', 'name' => '유동부채', 'type' => '부채', 'sort_order' => 21],
            ['code' => '2110', 'name' => '매입채무', 'type' => '부채', 'sort_order' => 22],
            ['code' => '2120', 'name' => '미지급금', 'type' => '부채', 'sort_order' => 23],
            ['code' => '2130', 'name' => '예수금', 'type' => '부채', 'sort_order' => 24],

            // 자본
            ['code' => '3000', 'name' => '자본', 'type' => '자본', 'sort_order' => 30],
            ['code' => '3100', 'name' => '자본금', 'type' => '자본', 'sort_order' => 31],
            ['code' => '3200', 'name' => '이익잉여금', 'type' => '자본', 'sort_order' => 32],

            // 수익
            ['code' => '4000', 'name' => '수익', 'type' => '수익', 'sort_order' => 40],
            ['code' => '4100', 'name' => '매출', 'type' => '수익', 'sort_order' => 41],
            ['code' => '4110', 'name' => '서비스매출', 'type' => '수익', 'sort_order' => 42],
            ['code' => '4120', 'name' => '상품매출', 'type' => '수익', 'sort_order' => 43],
            ['code' => '4200', 'name' => '영업외수익', 'type' => '수익', 'sort_order' => 44],
            ['code' => '4210', 'name' => '이자수익', 'type' => '수익', 'sort_order' => 45],

            // 비용
            ['code' => '5000', 'name' => '비용', 'type' => '비용', 'sort_order' => 50],
            ['code' => '5100', 'name' => '매출원가', 'type' => '비용', 'sort_order' => 51],
            ['code' => '5200', 'name' => '판매비와관리비', 'type' => '비용', 'sort_order' => 52],
            ['code' => '5210', 'name' => '급여', 'type' => '비용', 'sort_order' => 53],
            ['code' => '5220', 'name' => '복리후생비', 'type' => '비용', 'sort_order' => 54],
            ['code' => '5230', 'name' => '여비교통비', 'type' => '비용', 'sort_order' => 55],
            ['code' => '5240', 'name' => '통신비', 'type' => '비용', 'sort_order' => 56],
            ['code' => '5250', 'name' => '소모품비', 'type' => '비용', 'sort_order' => 57],
            ['code' => '5260', 'name' => '접대비', 'type' => '비용', 'sort_order' => 58],
            ['code' => '5270', 'name' => '광고선전비', 'type' => '비용', 'sort_order' => 59],
            ['code' => '5280', 'name' => '지급임차료', 'type' => '비용', 'sort_order' => 60],
            ['code' => '5290', 'name' => '세금과공과', 'type' => '비용', 'sort_order' => 61],
            ['code' => '5300', 'name' => '영업외비용', 'type' => '비용', 'sort_order' => 62],
            ['code' => '5310', 'name' => '이자비용', 'type' => '비용', 'sort_order' => 63],
        ];

        foreach ($accounts as $account) {
            Account::firstOrCreate(['code' => $account['code']], $account);
        }
    }
}
