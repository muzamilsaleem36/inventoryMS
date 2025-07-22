<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'credit_limit',
        'current_balance',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the sales for the customer.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the total purchases amount for the customer.
     */
    public function getTotalPurchases()
    {
        return $this->sales()->where('status', 'completed')->sum('total_amount');
    }

    /**
     * Get the total orders count for the customer.
     */
    public function getTotalOrders()
    {
        return $this->sales()->where('status', 'completed')->count();
    }

    /**
     * Scope for active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the customer's full name with phone.
     */
    public function getDisplayName()
    {
        return $this->name . ' (' . $this->phone . ')';
    }
} 