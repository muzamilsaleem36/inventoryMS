<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the sale that owns the sale item.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the product that owns the sale item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the profit for this sale item.
     */
    public function getProfit()
    {
        $purchasePrice = $this->product->purchase_price;
        return ($this->unit_price - $purchasePrice) * $this->quantity;
    }
} 