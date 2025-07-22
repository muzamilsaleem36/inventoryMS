<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'user_id',
        'store_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'amount_paid',
        'amount_due',
        'status',
        'order_date',
        'delivery_date',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'order_date' => 'date',
        'delivery_date' => 'date',
    ];

    /**
     * Get the supplier that owns the purchase.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user that owns the purchase.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the store that owns the purchase.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the purchase items for the purchase.
     */
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Get the total quantity of items purchased.
     */
    public function getTotalQuantity()
    {
        return $this->purchaseItems()->sum('quantity');
    }

    /**
     * Generate purchase number.
     */
    public static function generatePurchaseNumber()
    {
        $lastPurchase = static::latest()->first();
        $number = $lastPurchase ? (int) substr($lastPurchase->purchase_number, 4) + 1 : 1;
        return 'PUR-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for completed purchases.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending purchases.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
} 