<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => '연차',
                'code' => 'ANNUAL',
                'description' => '근로기준법에 따른 연차 휴가',
                'default_days' => 15,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#3B82F6',
            ],
            [
                'name' => '병가',
                'code' => 'SICK',
                'description' => '질병으로 인한 휴가',
                'default_days' => 0,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#EF4444',
            ],
            [
                'name' => '경조사',
                'code' => 'FAMILY',
                'description' => '경조사 관련 휴가',
                'default_days' => 0,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#8B5CF6',
            ],
            [
                'name' => '출산휴가',
                'code' => 'MATERNITY',
                'description' => '출산 전후 휴가',
                'default_days' => 90,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#EC4899',
            ],
            [
                'name' => '육아휴직',
                'code' => 'PARENTAL',
                'description' => '육아를 위한 휴직',
                'default_days' => 0,
                'is_paid' => false,
                'is_active' => true,
                'color' => '#F59E0B',
            ],
            [
                'name' => '무급휴가',
                'code' => 'UNPAID',
                'description' => '개인 사유 무급 휴가',
                'default_days' => 0,
                'is_paid' => false,
                'is_active' => true,
                'color' => '#6B7280',
            ],
            [
                'name' => '공가',
                'code' => 'OFFICIAL',
                'description' => '법정 공휴일 대체 등',
                'default_days' => 0,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#10B981',
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::firstOrCreate(['code' => $type['code']], $type);
        }
    }
}
