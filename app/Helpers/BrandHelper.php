<?php

namespace App\Helpers;

use App\Models\Setting;

class BrandHelper
{
    /**
     * Get the developer credit message
     */
    public static function getDeveloperCredit(): string
    {
        return Setting::where('key', 'developer_credit')->value('value') ?? 'Made by Conzec Technologies';
    }

    /**
     * Get the WhatsApp contact information
     */
    public static function getWhatsAppContact(): string
    {
        return Setting::where('key', 'whatsapp_contact')->value('value') ?? '+923325223746';
    }

    /**
     * Get the complete branding message with WhatsApp contact
     */
    public static function getCompleteBranding(): string
    {
        return 'Made by Conzec Technologies. Contact WhatsApp ' . self::getWhatsAppContact();
    }

    /**
     * Get the system version
     */
    public static function getSystemVersion(): string
    {
        return Setting::where('key', 'system_version')->value('value') ?? '1.0.0';
    }

    /**
     * Get the full branding footer
     */
    public static function getBrandingFooter(): string
    {
        $branding = self::getCompleteBranding();
        $version = self::getSystemVersion();
        
        return "{$branding} | Version {$version}";
    }

    /**
     * Get the powered by text for receipts
     */
    public static function getPoweredBy(): string
    {
        return 'Powered by Conzec Technologies POS System';
    }

    /**
     * Get the company website
     */
    public static function getCompanyWebsite(): string
    {
        return 'https://conzec.com';
    }

    /**
     * Get the company name
     */
    public static function getCompanyName(): string
    {
        return 'Conzec Technologies';
    }

    /**
     * Get the shop name from settings
     */
    public static function getShopName(): string
    {
        return Setting::where('key', 'shop_name')->value('value') ?? 'POS System';
    }

    /**
     * Get the shop logo path
     */
    public static function getShopLogo(): ?string
    {
        return Setting::where('key', 'shop_logo')->value('value');
    }

    /**
     * Get the currency symbol
     */
    public static function getCurrencySymbol(): string
    {
        return Setting::where('key', 'currency_symbol')->value('value') ?? '$';
    }

    /**
     * Get the currency code
     */
    public static function getCurrency(): string
    {
        return Setting::where('key', 'currency')->value('value') ?? 'USD';
    }

    /**
     * Get the tax rate
     */
    public static function getTaxRate(): float
    {
        return (float) Setting::where('key', 'tax_rate')->value('value') ?? 0.0;
    }

    /**
     * Format currency amount
     */
    public static function formatCurrency(float $amount): string
    {
        $symbol = self::getCurrencySymbol();
        return $symbol . number_format($amount, 2);
    }

    /**
     * Get the shop contact information
     */
    public static function getShopContact(): array
    {
        return [
            'name' => Setting::where('key', 'shop_name')->value('value') ?? '',
            'address' => Setting::where('key', 'shop_address')->value('value') ?? '',
            'phone' => Setting::where('key', 'shop_phone')->value('value') ?? '',
            'email' => Setting::where('key', 'shop_email')->value('value') ?? '',
            'website' => Setting::where('key', 'shop_website')->value('value') ?? '',
        ];
    }

    /**
     * Get the receipt footer with branding
     */
    public static function getReceiptFooter(): string
    {
        $receiptFooter = Setting::where('key', 'receipt_footer')->value('value') ?? 'Thank you for your business!';
        $branding = self::getPoweredBy();
        
        return $receiptFooter . "\n\n" . $branding;
    }
} 