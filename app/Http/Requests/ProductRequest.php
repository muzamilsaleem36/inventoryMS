<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can('manage_products');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2'
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'min:2',
                'alpha_num',
                Rule::unique('products', 'code')->ignore($productId)
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                'min:8',
                'regex:/^[0-9]+$/',
                Rule::unique('products', 'barcode')->ignore($productId)
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
                'integer'
            ],
            'purchase_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'selling_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
                'gte:purchase_price'
            ],
            'stock_quantity' => [
                'required',
                'integer',
                'min:0',
                'max:999999'
            ],
            'min_stock_level' => [
                'required',
                'integer',
                'min:0',
                'max:999999'
            ],
            'max_stock_level' => [
                'required',
                'integer',
                'min:1',
                'max:999999',
                'gt:min_stock_level'
            ],
            'unit' => [
                'required',
                'string',
                'max:20',
                'in:pcs,kg,g,l,ml,box,pack,bottle,can,dozen,meter,cm,inch,ft,yard,sqft,sqm'
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
            'track_stock' => [
                'boolean'
            ],
            'is_active' => [
                'boolean'
            ],
            'store_id' => [
                'nullable',
                'exists:stores,id',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!Auth::user()->hasRole('admin') && $value) {
                        $fail('Only administrators can assign products to specific stores.');
                    }
                }
            ],
            'remove_image' => [
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.min' => 'Product name must be at least 2 characters.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'code.required' => 'Product code is required.',
            'code.unique' => 'This product code is already taken.',
            'code.alpha_num' => 'Product code must contain only letters and numbers.',
            'barcode.unique' => 'This barcode is already in use.',
            'barcode.regex' => 'Barcode must contain only numbers.',
            'barcode.min' => 'Barcode must be at least 8 digits.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category does not exist.',
            'purchase_price.required' => 'Purchase price is required.',
            'purchase_price.min' => 'Purchase price cannot be negative.',
            'purchase_price.max' => 'Purchase price cannot exceed $999,999.99.',
            'selling_price.required' => 'Selling price is required.',
            'selling_price.min' => 'Selling price cannot be negative.',
            'selling_price.max' => 'Selling price cannot exceed $999,999.99.',
            'selling_price.gte' => 'Selling price should be greater than or equal to purchase price.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.min' => 'Stock quantity cannot be negative.',
            'stock_quantity.max' => 'Stock quantity cannot exceed 999,999.',
            'min_stock_level.required' => 'Minimum stock level is required.',
            'min_stock_level.min' => 'Minimum stock level cannot be negative.',
            'min_stock_level.max' => 'Minimum stock level cannot exceed 999,999.',
            'max_stock_level.required' => 'Maximum stock level is required.',
            'max_stock_level.min' => 'Maximum stock level must be at least 1.',
            'max_stock_level.max' => 'Maximum stock level cannot exceed 999,999.',
            'max_stock_level.gt' => 'Maximum stock level must be greater than minimum stock level.',
            'unit.required' => 'Unit is required.',
            'unit.in' => 'Please select a valid unit.',
            'image.image' => 'File must be an image.',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Image size cannot exceed 2MB.',
            'image.dimensions' => 'Image dimensions must be between 100x100 and 2000x2000 pixels.',
            'store_id.exists' => 'Selected store does not exist.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'purchase_price' => 'purchase price',
            'selling_price' => 'selling price',
            'stock_quantity' => 'stock quantity',
            'min_stock_level' => 'minimum stock level',
            'max_stock_level' => 'maximum stock level',
            'store_id' => 'store',
            'is_active' => 'active status',
            'track_stock' => 'stock tracking'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'track_stock' => $this->boolean('track_stock', true),
            'is_active' => $this->boolean('is_active', true),
            'remove_image' => $this->boolean('remove_image', false)
        ]);
    }

    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Ensure numeric fields are properly cast
        if (isset($validated['purchase_price'])) {
            $validated['purchase_price'] = (float) $validated['purchase_price'];
        }
        
        if (isset($validated['selling_price'])) {
            $validated['selling_price'] = (float) $validated['selling_price'];
        }
        
        if (isset($validated['stock_quantity'])) {
            $validated['stock_quantity'] = (int) $validated['stock_quantity'];
        }
        
        if (isset($validated['min_stock_level'])) {
            $validated['min_stock_level'] = (int) $validated['min_stock_level'];
        }
        
        if (isset($validated['max_stock_level'])) {
            $validated['max_stock_level'] = (int) $validated['max_stock_level'];
        }
        
        return $validated;
    }
} 