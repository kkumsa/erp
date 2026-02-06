<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LeaveType extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'description',
        'default_days',
        'is_paid',
        'is_active',
        'color',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'days_per_year', 'is_active'])
            ->logOnlyDirty();
    }
}
