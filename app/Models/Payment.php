<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Scopes\FinanceDepartmentScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public string $financeScopeMode = 'recorder';

    protected static function booted(): void
    {
        static::addGlobalScope(new FinanceDepartmentScope);
    }

    protected $fillable = [
        'payment_number',
        'payable_type',
        'payable_id',
        'payment_date',
        'amount',
        'method',
        'account_id',
        'reference',
        'note',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'method' => PaymentMethod::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'method', 'payment_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $prefix = 'PAY-' . date('Ymd') . '-';
                $lastNumber = static::withoutGlobalScopes()
                    ->withTrashed()
                    ->where('payment_number', 'like', $prefix . '%')
                    ->orderByDesc('payment_number')
                    ->value('payment_number');

                $nextSeq = 1;
                if ($lastNumber) {
                    $lastSeq = (int) substr($lastNumber, strlen($prefix));
                    $nextSeq = $lastSeq + 1;
                }

                $payment->payment_number = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            }
        });

        static::saved(function ($payment) {
            if ($payment->payable instanceof Invoice) {
                $payment->payable->updatePaymentStatus();
            }
        });
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
