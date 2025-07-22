<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'is_active',
        'store_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Get the store that the user belongs to.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the sales for the user.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the purchases for the user.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get the expenses for the user.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the activity logs for the user.
     */
    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is manager.
     */
    public function isManager()
    {
        return $this->hasRole('manager');
    }

    /**
     * Check if user is cashier.
     */
    public function isCashier()
    {
        return $this->hasRole('cashier');
    }
} 