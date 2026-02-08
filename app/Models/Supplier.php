<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\PurchaseOrderStatus;
use App\Enums\SupplierPaymentTerms;
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

    protected $casts = [
        'status' => ActiveStatus::class,
        'payment_terms' => SupplierPaymentTerms::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['company_name', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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
            ->whereIn('status', [PurchaseOrderStatus::Ordered, PurchaseOrderStatus::PartiallyReceived, PurchaseOrderStatus::Received])
            ->sum('total_amount');
    }
}
