<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'project_id',
        'order_date',
        'expected_date',
        'received_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
        'note',
        'shipping_address',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'approved_by'])
            ->logOldValues()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($po) {
            if (empty($po->po_number)) {
                $po->po_number = 'PO-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // 금액 재계산
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('amount');
        $this->tax_amount = $this->items->sum(function ($item) {
            return $item->amount * ($item->tax_rate / 100);
        });
        $this->total_amount = $this->subtotal + $this->tax_amount;
        $this->save();
    }

    // 입고 상태 업데이트
    public function updateReceiveStatus(): void
    {
        $totalQty = $this->items->sum('quantity');
        $receivedQty = $this->items->sum('received_quantity');

        if ($receivedQty >= $totalQty) {
            $this->status = '입고완료';
            $this->received_date = now();
        } elseif ($receivedQty > 0) {
            $this->status = '부분입고';
        }

        $this->save();
    }
}
