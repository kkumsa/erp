<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => '교통비',
                'code' => 'TRANSPORT',
                'description' => '출장, 이동 관련 교통비',
                'is_active' => true,
                'color' => '#3B82F6',
            ],
            [
                'name' => '식대',
                'code' => 'MEAL',
                'description' => '업무 관련 식사비',
                'is_active' => true,
                'color' => '#10B981',
            ],
            [
                'name' => '숙박비',
                'code' => 'ACCOMMODATION',
                'description' => '출장 숙박비',
                'is_active' => true,
                'color' => '#8B5CF6',
            ],
            [
                'name' => '사무용품',
                'code' => 'OFFICE_SUPPLIES',
                'description' => '사무용품 구매비',
                'is_active' => true,
                'color' => '#F59E0B',
            ],
            [
                'name' => '소프트웨어',
                'code' => 'SOFTWARE',
                'description' => '소프트웨어 구매/구독비',
                'is_active' => true,
                'color' => '#EC4899',
            ],
            [
                'name' => '장비/기기',
                'code' => 'EQUIPMENT',
                'description' => '업무용 장비 구매비',
                'is_active' => true,
                'color' => '#6366F1',
            ],
            [
                'name' => '통신비',
                'code' => 'COMMUNICATION',
                'description' => '전화, 인터넷 등 통신비',
                'is_active' => true,
                'color' => '#14B8A6',
            ],
            [
                'name' => '교육/훈련',
                'code' => 'TRAINING',
                'description' => '교육, 세미나, 컨퍼런스 비용',
                'is_active' => true,
                'color' => '#F97316',
            ],
            [
                'name' => '마케팅',
                'code' => 'MARKETING',
                'description' => '광고, 홍보 관련 비용',
                'is_active' => true,
                'color' => '#EF4444',
            ],
            [
                'name' => '기타',
                'code' => 'OTHER',
                'description' => '기타 비용',
                'is_active' => true,
                'color' => '#6B7280',
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(['code' => $category['code']], $category);
        }
    }
}
