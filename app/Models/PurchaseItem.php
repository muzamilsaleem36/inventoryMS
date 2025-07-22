<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_cost',
        'discount_amount',
        'total_cost',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Get the purchase that owns the purchase item.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the product that owns the purchase item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
} 