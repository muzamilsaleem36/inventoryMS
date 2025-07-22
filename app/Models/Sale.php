<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'customer_id',
        'user_id',
        'store_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'amount_paid',
        'change_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the sale.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user that owns the sale.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the store that owns the sale.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the sale items for the sale.
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the total quantity of items sold.
     */
    public function getTotalQuantity()
    {
        return $this->saleItems()->sum('quantity');
    }

    /**
     * Generate sale number.
     */
    public static function generateSaleNumber()
    {
        $lastSale = static::latest()->first();
        $number = $lastSale ? (int) substr($lastSale->sale_number, 4) + 1 : 1;
        return 'SAL-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for completed sales.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for today's sales.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this month's sales.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }
} 