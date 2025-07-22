<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-Z\s]+$/'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
            'password_confirmation' => [
                $isUpdate ? 'nullable' : 'required',
                'string',
                'same:password'
            ],
            'role' => [
                'required',
                'string',
                'exists:roles,name',
                'in:admin,manager,cashier'
            ],
            'store_id' => [
                'nullable',
                'exists:stores,id',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($this->role === 'admin' && $value) {
                        $fail('Admin users cannot be assigned to specific stores.');
                    }
                }
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\d\s\-\+\(\)]+$/'
            ],
            'address' => [
                'nullable',
                'string',
                'max:500'
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:1024',
                'dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000'
            ],
            'is_active' => [
                'boolean'
            ],
            'email_verified_at' => [
                'nullable',
                'date'
            ],
            'timezone' => [
                'nullable',
                'string',
                'max:50',
                'in:UTC,America/New_York,America/Chicago,America/Denver,America/Los_Angeles,Europe/London,Europe/Paris,Asia/Tokyo,Asia/Shanghai,Australia/Sydney'
            ],
            'language' => [
                'nullable',
                'string',
                'max:5',
                'in:en,es,fr,de,it,pt,zh,ja,ko,ar'
            ],
            'two_factor_enabled' => [
                'boolean'
            ],
            'notification_preferences' => [
                'nullable',
                'array'
            ],
            'notification_preferences.email' => [
                'boolean'
            ],
            'notification_preferences.sms' => [
                'boolean'
            ],
            'notification_preferences.push' => [
                'boolean'
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
            'name.required' => 'Name is required.',
            'name.min' => 'Name must be at least 2 characters.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'name.regex' => 'Name can only contain letters and spaces.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Email address cannot exceed 255 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'password_confirmation.required' => 'Password confirmation is required.',
            'password_confirmation.same' => 'Password confirmation must match the password.',
            'role.required' => 'Role is required.',
            'role.exists' => 'Selected role does not exist.',
            'role.in' => 'Invalid role selected.',
            'store_id.exists' => 'Selected store does not exist.',
            'phone.regex' => 'Phone number format is invalid.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a file of type: jpeg, png, jpg, gif.',
            'avatar.max' => 'Avatar size cannot exceed 1MB.',
            'avatar.dimensions' => 'Avatar dimensions must be between 100x100 and 1000x1000 pixels.',
            'timezone.in' => 'Invalid timezone selected.',
            'language.in' => 'Invalid language selected.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'store_id' => 'store',
            'is_active' => 'active status',
            'email_verified_at' => 'email verification date',
            'two_factor_enabled' => 'two-factor authentication',
            'notification_preferences' => 'notification preferences',
            'notification_preferences.email' => 'email notifications',
            'notification_preferences.sms' => 'SMS notifications',
            'notification_preferences.push' => 'push notifications'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'two_factor_enabled' => $this->boolean('two_factor_enabled', false),
            'remove_avatar' => $this->boolean('remove_avatar', false)
        ]);

        // Set default notification preferences
        if (!$this->notification_preferences) {
            $this->merge([
                'notification_preferences' => [
                    'email' => true,
                    'sms' => false,
                    'push' => true
                ]
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateRolePermissions($validator);
            $this->validateStoreAssignment($validator);
        });
    }

    /**
     * Validate role-based permissions.
     */
    protected function validateRolePermissions($validator): void
    {
        // Prevent creating multiple admin users (optional business rule)
        if ($this->role === 'admin' && !$this->route('user')) {
            $adminCount = \App\Models\User::role('admin')->count();
            if ($adminCount >= 5) { // Limit to 5 admin users
                $validator->errors()->add(
                    'role',
                    'Maximum number of admin users reached.'
                );
            }
        }

        // Prevent self-role change for certain roles
        if ($this->route('user') && Auth::id() === $this->route('user')->id) {
            $currentRole = Auth::user()->getRoleNames()->first();
            if ($currentRole === 'admin' && $this->role !== 'admin') {
                $validator->errors()->add(
                    'role',
                    'You cannot change your own admin role.'
                );
            }
        }
    }

    /**
     * Validate store assignment based on role.
     */
    protected function validateStoreAssignment($validator): void
    {
        if ($this->role === 'cashier' && !$this->store_id) {
            $validator->errors()->add(
                'store_id',
                'Cashier users must be assigned to a store.'
            );
        }

        if ($this->role === 'manager' && !$this->store_id) {
            $validator->errors()->add(
                'store_id',
                'Manager users must be assigned to a store.'
            );
        }
    }
} 