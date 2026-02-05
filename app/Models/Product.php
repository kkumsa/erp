<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'description',
        'unit',
        'purchase_price',
        'selling_price',
        'min_stock',
        'max_stock',
        'is_active',
        'is_stockable',
        'barcode',
        'image_path',
        'meta',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_stockable' => 'boolean',
        'meta' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'purchase_price', 'selling_price', 'is_active'])
            ->logOnlyDirty();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // 총 재고 수량
    public function getTotalStockAttribute(): float
    {
        return $this->stocks->sum('quantity');
    }

    // 가용 재고 (예약 제외)
    public function getAvailableStockAttribute(): float
    {
        return $this->stocks->sum(function ($stock) {
            return $stock->quantity - $stock->reserved_quantity;
        });
    }

    // 재고 부족 여부
    public function getIsLowStockAttribute(): bool
    {
        return $this->total_stock <= $this->min_stock;
    }
}
