<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set($key, $value, $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    /**
     * Get all settings as key-value pairs.
     */
    public static function getAll()
    {
        return static::all()->pluck('value', 'key');
    }

    /**
     * Get settings by group.
     */
    public static function getByGroup($group)
    {
        return static::where('group', $group)->get()->pluck('value', 'key');
    }

    /**
     * Business settings helper methods.
     */
    public static function getBusinessName()
    {
        return static::get('business_name', 'Your Business Name');
    }

    public static function getBusinessAddress()
    {
        return static::get('business_address', 'Your Business Address');
    }

    public static function getBusinessPhone()
    {
        return static::get('business_phone', 'Your Phone Number');
    }

    public static function getBusinessEmail()
    {
        return static::get('business_email', 'your@email.com');
    }

    public static function getTaxRate()
    {
        return (float) static::get('tax_rate', 0.00);
    }

    public static function getCurrency()
    {
        return static::get('currency', 'USD');
    }

    public static function getReceiptFooter()
    {
        return static::get('receipt_footer', 'Thank you for your business!');
    }
} 