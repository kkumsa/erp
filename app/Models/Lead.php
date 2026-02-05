<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Lead extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'company_name',
        'contact_name',
        'email',
        'phone',
        'source',
        'status',
        'description',
        'expected_revenue',
        'assigned_to',
        'converted_customer_id',
        'converted_at',
    ];

    protected $casts = [
        'expected_revenue' => 'decimal:2',
        'converted_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'assigned_to', 'converted_customer_id'])
            ->logOnlyDirty();
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'converted_customer_id');
    }

    // 고객으로 전환
    public function convertToCustomer(): Customer
    {
        $customer = Customer::create([
            'company_name' => $this->company_name ?? $this->contact_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'type' => '고객',
            'status' => '활성',
            'assigned_to' => $this->assigned_to,
        ]);

        // 담당자 정보 생성
        if ($this->contact_name) {
            $customer->contacts()->create([
                'name' => $this->contact_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'is_primary' => true,
            ]);
        }

        $this->update([
            'status' => '전환완료',
            'converted_customer_id' => $customer->id,
            'converted_at' => now(),
        ]);

        return $customer;
    }
}
