<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Stock extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'quantity',
        'reserved_quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // 가용 수량
    public function getAvailableQuantityAttribute(): float
    {
        return $this->quantity - $this->reserved_quantity;
    }

    // 재고 증가
    public function addStock(float $quantity): void
    {
        $this->increment('quantity', $quantity);
    }

    // 재고 감소
    public function reduceStock(float $quantity): void
    {
        if ($this->available_quantity < $quantity) {
            throw new \Exception('가용 재고가 부족합니다.');
        }
        $this->decrement('quantity', $quantity);
    }

    // 예약
    public function reserve(float $quantity): void
    {
        if ($this->available_quantity < $quantity) {
            throw new \Exception('가용 재고가 부족합니다.');
        }
        $this->increment('reserved_quantity', $quantity);
    }

    // 예약 해제
    public function unreserve(float $quantity): void
    {
        $this->decrement('reserved_quantity', min($quantity, $this->reserved_quantity));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['quantity', 'reserved_quantity'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
