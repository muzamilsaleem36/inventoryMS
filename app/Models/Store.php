<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'manager_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the users for the store.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the products for the store.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the sales for the store.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the purchases for the store.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get the expenses for the store.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Scope for active stores.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 