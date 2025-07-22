<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'category',
        'payment_method',
        'expense_date',
        'receipt_image',
        'user_id',
        'store_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Get the user that owns the expense.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the store that owns the expense.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Scope for this month's expenses.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', now()->month)
                    ->whereYear('expense_date', now()->year);
    }

    /**
     * Scope for today's expenses.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('expense_date', today());
    }

    /**
     * Scope for expenses by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
} 