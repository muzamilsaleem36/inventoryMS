<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can('manage_customers');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $customerId = $this->route('customer') ? $this->route('customer')->id : null;
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-Z\s\-\.\']+$/'
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customerId)
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[\d\s\-\+\(\)]+$/'
            ],
            'address' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'city' => [
                'nullable',
                'string',
                'max:100'
            ],
            'state' => [
                'nullable',
                'string',
                'max:100'
            ],
            'postal_code' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[a-zA-Z0-9\s\-]+$/'
            ],
            'country' => [
                'nullable',
                'string',
                'max:100'
            ],
            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
                'after:1900-01-01'
            ],
            'gender' => [
                'nullable',
                'in:male,female,other,prefer_not_to_say'
            ],
            'credit_limit' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'current_balance' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'payment_terms' => [
                'nullable',
                'integer',
                'min:0',
                'max:365'
            ],
            'discount_percentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],
            'tax_number' => [
                'nullable',
                'string',
                'max:50'
            ],
            'company_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'is_active' => [
                'boolean'
            ],
            'loyalty_points' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999'
            ],
            'preferred_language' => [
                'nullable',
                'string',
                'max:5',
                'in:en,es,fr,de,it,pt,zh,ja,ko,ar'
            ],
            'marketing_consent' => [
                'boolean'
            ],
            'sms_consent' => [
                'boolean'
            ],
            'email_consent' => [
                'boolean'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'referral_source' => [
                'nullable',
                'string',
                'max:100'
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:1024',
                'dimensions:min_width=100,min_height=100,max_width=800,max_height=800'
            ],
            'emergency_contact_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\d\s\-\+\(\)]+$/'
            ],
            'remove_avatar' => [
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
            'name.required' => 'Customer name is required.',
            'name.min' => 'Customer name must be at least 2 characters.',
            'name.max' => 'Customer name cannot exceed 255 characters.',
            'name.regex' => 'Customer name can only contain letters, spaces, hyphens, dots, and apostrophes.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Email address cannot exceed 255 characters.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'phone.regex' => 'Phone number format is invalid.',
            'address.max' => 'Address cannot exceed 1000 characters.',
            'city.max' => 'City cannot exceed 100 characters.',
            'state.max' => 'State cannot exceed 100 characters.',
            'postal_code.max' => 'Postal code cannot exceed 20 characters.',
            'postal_code.regex' => 'Postal code format is invalid.',
            'country.max' => 'Country cannot exceed 100 characters.',
            'date_of_birth.date' => 'Please enter a valid date of birth.',
            'date_of_birth.before' => 'Date of birth cannot be in the future.',
            'date_of_birth.after' => 'Date of birth cannot be before 1900.',
            'gender.in' => 'Please select a valid gender option.',
            'credit_limit.numeric' => 'Credit limit must be a number.',
            'credit_limit.min' => 'Credit limit cannot be negative.',
            'credit_limit.max' => 'Credit limit cannot exceed $999,999.99.',
            'current_balance.numeric' => 'Current balance must be a number.',
            'current_balance.min' => 'Current balance cannot be negative.',
            'current_balance.max' => 'Current balance cannot exceed $999,999.99.',
            'payment_terms.integer' => 'Payment terms must be a number.',
            'payment_terms.min' => 'Payment terms cannot be negative.',
            'payment_terms.max' => 'Payment terms cannot exceed 365 days.',
            'discount_percentage.numeric' => 'Discount percentage must be a number.',
            'discount_percentage.min' => 'Discount percentage cannot be negative.',
            'discount_percentage.max' => 'Discount percentage cannot exceed 100%.',
            'tax_number.max' => 'Tax number cannot exceed 50 characters.',
            'company_name.max' => 'Company name cannot exceed 255 characters.',
            'loyalty_points.integer' => 'Loyalty points must be a number.',
            'loyalty_points.min' => 'Loyalty points cannot be negative.',
            'loyalty_points.max' => 'Loyalty points cannot exceed 999,999.',
            'preferred_language.in' => 'Please select a valid language.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'referral_source.max' => 'Referral source cannot exceed 100 characters.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a file of type: jpeg, png, jpg, gif.',
            'avatar.max' => 'Avatar size cannot exceed 1MB.',
            'avatar.dimensions' => 'Avatar dimensions must be between 100x100 and 800x800 pixels.',
            'emergency_contact_name.max' => 'Emergency contact name cannot exceed 255 characters.',
            'emergency_contact_phone.max' => 'Emergency contact phone cannot exceed 20 characters.',
            'emergency_contact_phone.regex' => 'Emergency contact phone format is invalid.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'date_of_birth' => 'date of birth',
            'credit_limit' => 'credit limit',
            'current_balance' => 'current balance',
            'payment_terms' => 'payment terms',
            'discount_percentage' => 'discount percentage',
            'tax_number' => 'tax number',
            'company_name' => 'company name',
            'is_active' => 'active status',
            'loyalty_points' => 'loyalty points',
            'preferred_language' => 'preferred language',
            'marketing_consent' => 'marketing consent',
            'sms_consent' => 'SMS consent',
            'email_consent' => 'email consent',
            'referral_source' => 'referral source',
            'emergency_contact_name' => 'emergency contact name',
            'emergency_contact_phone' => 'emergency contact phone'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'marketing_consent' => $this->boolean('marketing_consent', false),
            'sms_consent' => $this->boolean('sms_consent', false),
            'email_consent' => $this->boolean('email_consent', false),
            'remove_avatar' => $this->boolean('remove_avatar', false)
        ]);

        // Set default values
        if (!$this->credit_limit) {
            $this->merge(['credit_limit' => 0]);
        }

        if (!$this->current_balance) {
            $this->merge(['current_balance' => 0]);
        }

        if (!$this->loyalty_points) {
            $this->merge(['loyalty_points' => 0]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateCreditLimit($validator);
            $this->validateBalance($validator);
            $this->validateConsent($validator);
        });
    }

    /**
     * Validate credit limit against current balance.
     */
    protected function validateCreditLimit($validator): void
    {
        if ($this->credit_limit && $this->current_balance) {
            if ($this->current_balance > $this->credit_limit) {
                $validator->errors()->add(
                    'current_balance',
                    'Current balance cannot exceed credit limit.'
                );
            }
        }
    }

    /**
     * Validate balance constraints.
     */
    protected function validateBalance($validator): void
    {
        // Check if customer has any pending sales that would exceed credit limit
        if ($this->credit_limit && $this->route('customer')) {
            $customer = $this->route('customer');
            $pendingSales = $customer->sales()->where('payment_status', 'pending')->sum('amount_due');
            
            if (($this->current_balance + $pendingSales) > $this->credit_limit) {
                $validator->errors()->add(
                    'credit_limit',
                    'Credit limit cannot be less than current balance plus pending sales.'
                );
            }
        }
    }

    /**
     * Validate consent requirements.
     */
    protected function validateConsent($validator): void
    {
        // If email is provided, email consent should be considered
        if ($this->email && !$this->email_consent) {
            // This is just a warning, not an error
            // Could be implemented as a business rule
        }

        // If customer is under 18, require additional consent validation
        if ($this->date_of_birth) {
            $age = now()->diffInYears($this->date_of_birth);
            if ($age < 18 && !$this->emergency_contact_name) {
                $validator->errors()->add(
                    'emergency_contact_name',
                    'Emergency contact is required for customers under 18.'
                );
            }
        }
    }
} 