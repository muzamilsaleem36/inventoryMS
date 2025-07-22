<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'tax_number',
        'credit_limit',
        'current_balance',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the purchases for the supplier.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get the total purchases amount from the supplier.
     */
    public function getTotalPurchases()
    {
        return $this->purchases()->where('status', 'completed')->sum('total_amount');
    }

    /**
     * Get the total orders count from the supplier.
     */
    public function getTotalOrders()
    {
        return $this->purchases()->where('status', 'completed')->count();
    }

    /**
     * Scope for active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the supplier's display name with company.
     */
    public function getDisplayName()
    {
        return $this->name . ' (' . $this->company_name . ')';
    }
} 