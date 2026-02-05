<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'work_minutes',
        'overtime_minutes',
        'status',
        'note',
        'check_in_ip',
        'check_out_ip',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // 근무 시간 (시:분 형식)
    public function getWorkTimeAttribute(): string
    {
        $hours = floor($this->work_minutes / 60);
        $minutes = $this->work_minutes % 60;
        return sprintf('%d시간 %d분', $hours, $minutes);
    }

    // 초과 근무 시간 (시:분 형식)
    public function getOvertimeAttribute(): string
    {
        $hours = floor($this->overtime_minutes / 60);
        $minutes = $this->overtime_minutes % 60;
        return sprintf('%d시간 %d분', $hours, $minutes);
    }
}
