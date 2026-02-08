<?php

namespace App\Models;

use App\Scopes\FinanceDepartmentScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BankDeposit extends Model
{
    use HasFactory, LogsActivity;

    public string $financeScopeMode = 'creator';

    protected static function booted(): void
    {
        static::addGlobalScope(new FinanceDepartmentScope);
    }

    protected $fillable = [
        'deposited_at',
        'depositor_name',
        'amount',
        'transaction_number',
        'bank_account',
        'memo',
        'processed_at',
        'payment_id',
        'created_by',
    ];

    protected $casts = [
        'deposited_at' => 'datetime',
        'processed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['depositor_name', 'amount', 'processed_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 처리 완료 여부
     */
    public function getIsProcessedAttribute(): bool
    {
        return $this->processed_at !== null;
    }
}
