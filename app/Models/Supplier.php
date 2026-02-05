<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'company_name',
        'code',
        'business_number',
        'representative',
        'contact_name',
        'phone',
        'fax',
        'email',
        'address',
        'bank_name',
        'bank_account',
        'bank_holder',
        'status',
        'payment_terms',
        'payment_days',
        'note',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['company_name', 'status'])
            ->logOnlyDirty();
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // 총 거래액
    public function getTotalPurchaseAmountAttribute(): float
    {
        return $this->purchaseOrders()
            ->whereIn('status', ['발주완료', '부분입고', '입고완료'])
            ->sum('total_amount');
    }
}
