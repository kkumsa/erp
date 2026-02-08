<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StockMovement extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'reference_number',
        'warehouse_id',
        'product_id',
        'type',
        'quantity',
        'before_quantity',
        'after_quantity',
        'unit_cost',
        'reference_type',
        'reference_id',
        'destination_warehouse_id',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'before_quantity' => 'decimal:2',
        'after_quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'type' => StockMovementType::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movement) {
            if (empty($movement->reference_number)) {
                $movement->reference_number = 'STK-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });

        static::created(function ($movement) {
            $movement->applyMovement();
        });
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 재고 변동 적용
    protected function applyMovement(): void
    {
        // 출발 창고 재고 업데이트
        $stock = Stock::firstOrCreate(
            ['warehouse_id' => $this->warehouse_id, 'product_id' => $this->product_id],
            ['quantity' => 0, 'reserved_quantity' => 0]
        );

        $this->before_quantity = $stock->quantity;

        if (in_array($this->type, [StockMovementType::Incoming, StockMovementType::ReturnStock])) {
            $stock->addStock(abs($this->quantity));
        } elseif ($this->type === StockMovementType::Outgoing) {
            $stock->reduceStock(abs($this->quantity));
        } elseif ($this->type === StockMovementType::Adjustment) {
            $stock->quantity = $this->quantity;
            $stock->save();
        } elseif ($this->type === StockMovementType::Transfer && $this->destination_warehouse_id) {
            // 출발 창고에서 차감
            $stock->reduceStock(abs($this->quantity));

            // 도착 창고에 추가
            $destStock = Stock::firstOrCreate(
                ['warehouse_id' => $this->destination_warehouse_id, 'product_id' => $this->product_id],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );
            $destStock->addStock(abs($this->quantity));
        }

        $this->after_quantity = $stock->fresh()->quantity;
        $this->saveQuietly();
    }

    // 입고 처리 (발주서에서)
    public static function createFromPurchaseOrder(PurchaseOrderItem $item, float $quantity, int $warehouseId, ?int $userId = null): self
    {
        return static::create([
            'warehouse_id' => $warehouseId,
            'product_id' => $item->product_id,
            'type' => StockMovementType::Incoming,
            'quantity' => $quantity,
            'unit_cost' => $item->unit_price,
            'reference_type' => PurchaseOrder::class,
            'reference_id' => $item->purchase_order_id,
            'reason' => "발주서 #{$item->purchaseOrder->po_number} 입고",
            'created_by' => $userId,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'quantity', 'warehouse_id', 'product_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
