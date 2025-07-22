@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Business Settings</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="business-tab" data-bs-toggle="tab" href="#business" role="tab">
                                    <i class="fas fa-building"></i> Business Info
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="system-tab" data-bs-toggle="tab" href="#system" role="tab">
                                    <i class="fas fa-cogs"></i> System
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="features-tab" data-bs-toggle="tab" href="#features" role="tab">
                                    <i class="fas fa-star"></i> Features
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="notifications-tab" data-bs-toggle="tab" href="#notifications" role="tab">
                                    <i class="fas fa-bell"></i> Notifications
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3">
                            <!-- Business Info Tab -->
                            <div class="tab-pane fade show active" id="business" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="business_name">Business Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('business_name') is-invalid @enderror" 
                                                   id="business_name" name="business_name" 
                                                   value="{{ old('business_name', $settings['business_name'] ?? '') }}" required>
                                            @error('business_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="business_email">Email</label>
                                            <input type="email" class="form-control @error('business_email') is-invalid @enderror" 
                                                   id="business_email" name="business_email" 
                                                   value="{{ old('business_email', $settings['business_email'] ?? '') }}">
                                            @error('business_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="business_phone">Phone</label>
                                            <input type="text" class="form-control @error('business_phone') is-invalid @enderror" 
                                                   id="business_phone" name="business_phone" 
                                                   value="{{ old('business_phone', $settings['business_phone'] ?? '') }}">
                                            @error('business_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="business_website">Website</label>
                                            <input type="url" class="form-control @error('business_website') is-invalid @enderror" 
                                                   id="business_website" name="business_website" 
                                                   value="{{ old('business_website', $settings['business_website'] ?? '') }}">
                                            @error('business_website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="business_address">Address</label>
                                    <textarea class="form-control @error('business_address') is-invalid @enderror" 
                                              id="business_address" name="business_address" rows="3">{{ old('business_address', $settings['business_address'] ?? '') }}</textarea>
                                    @error('business_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="business_logo">Business Logo</label>
                                    <input type="file" class="form-control @error('business_logo') is-invalid @enderror" 
                                           id="business_logo" name="business_logo" accept="image/*">
                                    @error('business_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if(isset($settings['business_logo']))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $settings['business_logo']) }}" 
                                             alt="Business Logo" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="currency">Currency <span class="text-danger">*</span></label>
                                            <select class="form-select @error('currency') is-invalid @enderror" 
                                                    id="currency" name="currency" required>
                                                <option value="USD" {{ old('currency', $settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>US Dollar</option>
                                                <option value="EUR" {{ old('currency', $settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>Euro</option>
                                                <option value="GBP" {{ old('currency', $settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>British Pound</option>
                                                <option value="CAD" {{ old('currency', $settings['currency'] ?? '') == 'CAD' ? 'selected' : '' }}>Canadian Dollar</option>
                                                <option value="AUD" {{ old('currency', $settings['currency'] ?? '') == 'AUD' ? 'selected' : '' }}>Australian Dollar</option>
                                            </select>
                                            @error('currency')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="currency_symbol">Currency Symbol <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('currency_symbol') is-invalid @enderror" 
                                                   id="currency_symbol" name="currency_symbol" 
                                                   value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '$') }}" required>
                                            @error('currency_symbol')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="tax_rate">Default Tax Rate (%)</label>
                                            <input type="number" class="form-control @error('tax_rate') is-invalid @enderror" 
                                                   id="tax_rate" name="tax_rate" step="0.01" min="0" max="100"
                                                   value="{{ old('tax_rate', $settings['tax_rate'] ?? '') }}">
                                            @error('tax_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="low_stock_threshold">Low Stock Threshold <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('low_stock_threshold') is-invalid @enderror" 
                                                   id="low_stock_threshold" name="low_stock_threshold" min="0"
                                                   value="{{ old('low_stock_threshold', $settings['low_stock_threshold'] ?? '10') }}" required>
                                            @error('low_stock_threshold')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="receipt_header">Receipt Header</label>
                                            <input type="text" class="form-control @error('receipt_header') is-invalid @enderror" 
                                                   id="receipt_header" name="receipt_header" 
                                                   value="{{ old('receipt_header', $settings['receipt_header'] ?? '') }}">
                                            @error('receipt_header')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="receipt_footer">Receipt Footer</label>
                                            <input type="text" class="form-control @error('receipt_footer') is-invalid @enderror" 
                                                   id="receipt_footer" name="receipt_footer" 
                                                   value="{{ old('receipt_footer', $settings['receipt_footer'] ?? '') }}">
                                            @error('receipt_footer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- System Tab -->
                            <div class="tab-pane fade" id="system" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="timezone">Timezone <span class="text-danger">*</span></label>
                                            <select class="form-select @error('timezone') is-invalid @enderror" 
                                                    id="timezone" name="timezone" required>
                                                <option value="UTC" {{ old('timezone', $settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                                <option value="America/New_York" {{ old('timezone', $settings['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                                <option value="America/Chicago" {{ old('timezone', $settings['timezone'] ?? '') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                                <option value="America/Denver" {{ old('timezone', $settings['timezone'] ?? '') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                                <option value="America/Los_Angeles" {{ old('timezone', $settings['timezone'] ?? '') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                            </select>
                                            @error('timezone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="language">Language <span class="text-danger">*</span></label>
                                            <select class="form-select @error('language') is-invalid @enderror" 
                                                    id="language" name="language" required>
                                                <option value="en" {{ old('language', $settings['language'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                                                <option value="es" {{ old('language', $settings['language'] ?? '') == 'es' ? 'selected' : '' }}>Spanish</option>
                                                <option value="fr" {{ old('language', $settings['language'] ?? '') == 'fr' ? 'selected' : '' }}>French</option>
                                                <option value="de" {{ old('language', $settings['language'] ?? '') == 'de' ? 'selected' : '' }}>German</option>
                                            </select>
                                            @error('language')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="date_format">Date Format <span class="text-danger">*</span></label>
                                            <select class="form-select @error('date_format') is-invalid @enderror" 
                                                    id="date_format" name="date_format" required>
                                                <option value="Y-m-d" {{ old('date_format', $settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                                <option value="m/d/Y" {{ old('date_format', $settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                                <option value="d/m/Y" {{ old('date_format', $settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                                <option value="M d, Y" {{ old('date_format', $settings['date_format'] ?? '') == 'M d, Y' ? 'selected' : '' }}>Mon DD, YYYY</option>
                                            </select>
                                            @error('date_format')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="time_format">Time Format <span class="text-danger">*</span></label>
                                            <select class="form-select @error('time_format') is-invalid @enderror" 
                                                    id="time_format" name="time_format" required>
                                                <option value="H:i" {{ old('time_format', $settings['time_format'] ?? '') == 'H:i' ? 'selected' : '' }}>24 Hour (HH:MM)</option>
                                                <option value="g:i A" {{ old('time_format', $settings['time_format'] ?? '') == 'g:i A' ? 'selected' : '' }}>12 Hour (H:MM AM/PM)</option>
                                            </select>
                                            @error('time_format')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="theme">Theme <span class="text-danger">*</span></label>
                                            <select class="form-select @error('theme') is-invalid @enderror" 
                                                    id="theme" name="theme" required>
                                                <option value="light" {{ old('theme', $settings['theme'] ?? '') == 'light' ? 'selected' : '' }}>Light</option>
                                                <option value="dark" {{ old('theme', $settings['theme'] ?? '') == 'dark' ? 'selected' : '' }}>Dark</option>
                                                <option value="auto" {{ old('theme', $settings['theme'] ?? '') == 'auto' ? 'selected' : '' }}>Auto</option>
                                            </select>
                                            @error('theme')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="backup_frequency">Backup Frequency <span class="text-danger">*</span></label>
                                            <select class="form-select @error('backup_frequency') is-invalid @enderror" 
                                                    id="backup_frequency" name="backup_frequency" required>
                                                <option value="daily" {{ old('backup_frequency', $settings['backup_frequency'] ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
                                                <option value="weekly" {{ old('backup_frequency', $settings['backup_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                                <option value="monthly" {{ old('backup_frequency', $settings['backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                            </select>
                                            @error('backup_frequency')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Features Tab -->
                            <div class="tab-pane fade" id="features" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="enable_barcode" name="enable_barcode" value="1"
                                                   {{ old('enable_barcode', $settings['enable_barcode'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_barcode">
                                                Enable Barcode Generation
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="barcode_format">Barcode Format</label>
                                            <select class="form-select @error('barcode_format') is-invalid @enderror" 
                                                    id="barcode_format" name="barcode_format">
                                                <option value="CODE128" {{ old('barcode_format', $settings['barcode_format'] ?? '') == 'CODE128' ? 'selected' : '' }}>CODE128</option>
                                                <option value="CODE39" {{ old('barcode_format', $settings['barcode_format'] ?? '') == 'CODE39' ? 'selected' : '' }}>CODE39</option>
                                                <option value="EAN13" {{ old('barcode_format', $settings['barcode_format'] ?? '') == 'EAN13' ? 'selected' : '' }}>EAN13</option>
                                                <option value="EAN8" {{ old('barcode_format', $settings['barcode_format'] ?? '') == 'EAN8' ? 'selected' : '' }}>EAN8</option>
                                                <option value="UPC_A" {{ old('barcode_format', $settings['barcode_format'] ?? '') == 'UPC_A' ? 'selected' : '' }}>UPC-A</option>
                                                <option value="UPC_E" {{ old('barcode_format', $settings['barcode_format'] ?? '') == 'UPC_E' ? 'selected' : '' }}>UPC-E</option>
                                            </select>
                                            @error('barcode_format')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="enable_multi_store" name="enable_multi_store" value="1"
                                                   {{ old('enable_multi_store', $settings['enable_multi_store'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_multi_store">
                                                Enable Multi-Store Support
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="enable_expenses" name="enable_expenses" value="1"
                                                   {{ old('enable_expenses', $settings['enable_expenses'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_expenses">
                                                Enable Expense Tracking
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="enable_activity_logs" name="enable_activity_logs" value="1"
                                                   {{ old('enable_activity_logs', $settings['enable_activity_logs'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_activity_logs">
                                                Enable Activity Logging
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notifications Tab -->
                            <div class="tab-pane fade" id="notifications" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="email_notifications" name="email_notifications" value="1"
                                                   {{ old('email_notifications', $settings['email_notifications'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="email_notifications">
                                                Enable Email Notifications
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="sms_notifications" name="sms_notifications" value="1"
                                                   {{ old('sms_notifications', $settings['sms_notifications'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sms_notifications">
                                                Enable SMS Notifications
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="notification_low_stock" name="notification_low_stock" value="1"
                                                   {{ old('notification_low_stock', $settings['notification_low_stock'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="notification_low_stock">
                                                Low Stock Notifications
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="notification_new_order" name="notification_new_order" value="1"
                                                   {{ old('notification_new_order', $settings['notification_new_order'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="notification_new_order">
                                                New Order Notifications
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 