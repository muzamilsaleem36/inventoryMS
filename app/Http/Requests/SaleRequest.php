<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can('manage_sales');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_id' => [
                'nullable',
                'exists:customers,id',
                'integer'
            ],
            'items' => [
                'required',
                'array',
                'min:1',
                'max:50'
            ],
            'items.*.product_id' => [
                'required',
                'exists:products,id',
                'integer'
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:999'
            ],
            'items.*.price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'discount_type' => [
                'required',
                'in:none,percentage,fixed'
            ],
            'discount_value' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->discount_type === 'percentage' && $value > 100) {
                        $fail('Discount percentage cannot exceed 100%.');
                    }
                    if ($this->discount_type === 'fixed' && $value > 999999.99) {
                        $fail('Discount amount cannot exceed $999,999.99.');
                    }
                }
            ],
            'tax_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],
            'tax_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'payment_method' => [
                'required',
                'in:cash,card,transfer,credit,cheque,mobile_payment'
            ],
            'payment_status' => [
                'required',
                'in:paid,partial,pending,failed'
            ],
            'amount_paid' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'amount_due' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500'
            ],
            'reference_number' => [
                'nullable',
                'string',
                'max:100'
            ],
            'sale_date' => [
                'nullable',
                'date',
                'before_or_equal:today'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_id.exists' => 'Selected customer does not exist.',
            'items.required' => 'At least one item is required for the sale.',
            'items.min' => 'At least one item is required for the sale.',
            'items.max' => 'Cannot add more than 50 items to a single sale.',
            'items.*.product_id.required' => 'Product is required for each item.',
            'items.*.product_id.exists' => 'One or more selected products do not exist.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.quantity.max' => 'Quantity cannot exceed 999.',
            'items.*.price.required' => 'Price is required for each item.',
            'items.*.price.min' => 'Price cannot be negative.',
            'items.*.price.max' => 'Price cannot exceed $999,999.99.',
            'discount_type.required' => 'Discount type is required.',
            'discount_type.in' => 'Invalid discount type selected.',
            'discount_value.min' => 'Discount value cannot be negative.',
            'tax_rate.min' => 'Tax rate cannot be negative.',
            'tax_rate.max' => 'Tax rate cannot exceed 100%.',
            'tax_amount.min' => 'Tax amount cannot be negative.',
            'tax_amount.max' => 'Tax amount cannot exceed $999,999.99.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Invalid payment method selected.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'Invalid payment status selected.',
            'amount_paid.min' => 'Amount paid cannot be negative.',
            'amount_paid.max' => 'Amount paid cannot exceed $999,999.99.',
            'amount_due.min' => 'Amount due cannot be negative.',
            'amount_due.max' => 'Amount due cannot exceed $999,999.99.',
            'notes.max' => 'Notes cannot exceed 500 characters.',
            'reference_number.max' => 'Reference number cannot exceed 100 characters.',
            'sale_date.before_or_equal' => 'Sale date cannot be in the future.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'customer',
            'discount_type' => 'discount type',
            'discount_value' => 'discount value',
            'tax_rate' => 'tax rate',
            'tax_amount' => 'tax amount',
            'payment_method' => 'payment method',
            'payment_status' => 'payment status',
            'amount_paid' => 'amount paid',
            'amount_due' => 'amount due',
            'reference_number' => 'reference number',
            'sale_date' => 'sale date'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'discount_type' => $this->discount_type ?? 'none',
            'payment_status' => $this->payment_status ?? 'paid',
            'sale_date' => $this->sale_date ?? now()->format('Y-m-d')
        ]);

        // Clean up discount value based on type
        if ($this->discount_type === 'none') {
            $this->merge(['discount_value' => null]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateItemsStock($validator);
            $this->validateDiscountValue($validator);
            $this->validatePaymentAmounts($validator);
        });
    }

    /**
     * Validate that products have sufficient stock.
     */
    protected function validateItemsStock($validator): void
    {
        if (!$this->items || !is_array($this->items)) {
            return;
        }

        foreach ($this->items as $index => $item) {
            if (!isset($item['product_id']) || !isset($item['quantity'])) {
                continue;
            }

            $product = \App\Models\Product::find($item['product_id']);
            
            if (!$product) {
                continue;
            }

            if ($product->track_stock && $product->stock_quantity < $item['quantity']) {
                $validator->errors()->add(
                    "items.{$index}.quantity",
                    "Insufficient stock for {$product->name}. Available: {$product->stock_quantity}"
                );
            }

            if (!$product->is_active) {
                $validator->errors()->add(
                    "items.{$index}.product_id",
                    "Product {$product->name} is not active."
                );
            }
        }
    }

    /**
     * Validate discount value based on subtotal.
     */
    protected function validateDiscountValue($validator): void
    {
        if ($this->discount_type === 'fixed' && $this->discount_value) {
            $subtotal = $this->calculateSubtotal();
            
            if ($this->discount_value > $subtotal) {
                $validator->errors()->add(
                    'discount_value',
                    'Discount amount cannot exceed the subtotal.'
                );
            }
        }
    }

    /**
     * Validate payment amounts.
     */
    protected function validatePaymentAmounts($validator): void
    {
        if ($this->payment_status === 'paid' && $this->amount_due > 0) {
            $validator->errors()->add(
                'payment_status',
                'Payment status cannot be "paid" when amount due is greater than 0.'
            );
        }

        if ($this->payment_status === 'pending' && $this->amount_paid > 0) {
            $validator->errors()->add(
                'payment_status',
                'Payment status cannot be "pending" when amount paid is greater than 0.'
            );
        }
    }

    /**
     * Calculate subtotal from items.
     */
    protected function calculateSubtotal(): float
    {
        if (!$this->items || !is_array($this->items)) {
            return 0;
        }

        $subtotal = 0;
        foreach ($this->items as $item) {
            if (isset($item['quantity']) && isset($item['price'])) {
                $subtotal += $item['quantity'] * $item['price'];
            }
        }

        return $subtotal;
    }
} 