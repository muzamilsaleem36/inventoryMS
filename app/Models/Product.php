<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'barcode',
        'description',
        'category_id',
        'purchase_price',
        'selling_price',
        'stock_quantity',
        'min_stock_level',
        'max_stock_level',
        'unit',
        'image',
        'track_stock',
        'is_active',
        'store_id',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'max_stock_level' => 'integer',
        'track_stock' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the store that owns the product.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the sale items for the product.
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the purchase items for the product.
     */
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Check if product is low stock.
     */
    public function isLowStock()
    {
        return $this->track_stock && $this->stock_quantity <= $this->min_stock_level;
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock()
    {
        return $this->track_stock && $this->stock_quantity <= 0;
    }

    /**
     * Get the profit margin.
     */
    public function getProfitMargin()
    {
        if ($this->purchase_price == 0) {
            return 0;
        }
        
        return (($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100;
    }

    /**
     * Adjust stock quantity.
     */
    public function adjustStock($quantity, $type = 'increase')
    {
        if ($type === 'increase') {
            $this->stock_quantity += $quantity;
        } else {
            $this->stock_quantity -= $quantity;
        }
        
        $this->save();
    }

    /**
     * Scope for active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->where('track_stock', true)
                    ->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }

    /**
     * Scope for out of stock products.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('track_stock', true)
                    ->where('stock_quantity', '<=', 0);
    }
} 