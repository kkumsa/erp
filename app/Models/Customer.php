<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\CustomerType;
use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'company_name',
        'business_number',
        'representative',
        'industry',
        'business_type',
        'phone',
        'fax',
        'email',
        'website',
        'address',
        'type',
        'status',
        'assigned_to',
        'note',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'type' => CustomerType::class,
        'status' => ActiveStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['company_name', 'type', 'status', 'assigned_to'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function primaryContact()
    {
        return $this->contacts()->where('is_primary', true)->first();
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // 총 거래액 계산
    public function getTotalRevenueAttribute(): float
    {
        return $this->invoices()->where('status', InvoiceStatus::Paid)->sum('total_amount');
    }
}
