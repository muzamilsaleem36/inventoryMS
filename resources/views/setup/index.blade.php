<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System Setup - Conzec Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .setup-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        
        .setup-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 900px;
            width: 100%;
            overflow: hidden;
        }
        
        .setup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .setup-body {
            padding: 30px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }
        
        .step-progress {
            position: absolute;
            top: 20px;
            left: 0;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 1;
            transition: width 0.3s ease;
        }
        
        .step {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            color: #6c757d;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .step.active {
            border-color: #667eea;
            color: #667eea;
            transform: scale(1.1);
        }
        
        .step.completed {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }
        
        .step-label {
            position: absolute;
            top: 50px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: #6c757d;
            white-space: nowrap;
            font-weight: 500;
        }
        
        .form-section {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .form-section.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .section-title {
            color: #667eea;
            margin-bottom: 25px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .tooltip-icon {
            color: #667eea;
            cursor: help;
            margin-left: 5px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .logo-preview {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            border: 2px dashed #e9ecef;
            padding: 10px;
            margin-top: 10px;
        }
        
        .developer-credit {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        
        .developer-credit strong {
            color: #667eea;
        }
        
        .preference-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
        }
        
        .preference-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        @media (max-width: 768px) {
            .setup-body {
                padding: 20px;
            }
            
            .step-indicator {
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .step {
                width: 35px;
                height: 35px;
                font-size: 12px;
            }
            
            .step-label {
                font-size: 10px;
                top: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <div class="setup-header">
                <h1><i class="fas fa-cogs"></i> Welcome to POS System Setup</h1>
                <p class="mb-0">Let's configure your Point of Sale system for your business</p>
            </div>
            
            <div class="setup-body">
                <div class="step-indicator">
                    <div class="step-progress" id="stepProgress"></div>
                    <div class="step active" data-step="1">
                        1
                        <div class="step-label">Business Info</div>
                    </div>
                    <div class="step" data-step="2">
                        2
                        <div class="step-label">Owner Details</div>
                    </div>
                    <div class="step" data-step="3">
                        3
                        <div class="step-label">Currency & Tax</div>
                    </div>
                    <div class="step" data-step="4">
                        4
                        <div class="step-label">POS Preferences</div>
                    </div>
                    <div class="step" data-step="5">
                        5
                        <div class="step-label">Logo & Finish</div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('setup.store') }}" enctype="multipart/form-data" id="setupForm">
                    @csrf
                    
                    <!-- Step 1: Business Information -->
                    <div class="form-section active" data-section="1">
                        <h3 class="section-title">
                            <i class="fas fa-store"></i> Business Information
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shop_name" class="form-label">
                                        Shop Name *
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="This will appear on receipts and reports"></i>
                                    </label>
                                    <input type="text" class="form-control" id="shop_name" name="shop_name" 
                                           value="{{ old('shop_name') }}" required placeholder="Enter your shop name">
                                    @error('shop_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shop_email" class="form-label">
                                        Shop Email *
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="Used for system notifications and receipt emails"></i>
                                    </label>
                                    <input type="email" class="form-control" id="shop_email" name="shop_email" 
                                           value="{{ old('shop_email') }}" required placeholder="shop@example.com">
                                    @error('shop_email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="shop_address" class="form-label">
                                Shop Address *
                                <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                   title="This address will appear on receipts and invoices"></i>
                            </label>
                            <textarea class="form-control" id="shop_address" name="shop_address" 
                                      rows="3" required placeholder="Enter your complete shop address">{{ old('shop_address') }}</textarea>
                            @error('shop_address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shop_phone" class="form-label">
                                        Shop Phone *
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="Customer service contact number"></i>
                                    </label>
                                    <input type="text" class="form-control" id="shop_phone" name="shop_phone" 
                                           value="{{ old('shop_phone') }}" required placeholder="+1 (555) 123-4567">
                                    @error('shop_phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shop_website" class="form-label">
                                        Shop Website
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="Optional: Your business website URL"></i>
                                    </label>
                                    <input type="url" class="form-control" id="shop_website" name="shop_website" 
                                           value="{{ old('shop_website') }}" placeholder="https://yourshop.com">
                                    @error('shop_website')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Owner Information -->
                    <div class="form-section" data-section="2">
                        <h3 class="section-title">
                            <i class="fas fa-user-tie"></i> Business Owner Information
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_name" class="form-label">
                                        Business Owner's Full Name *
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="This will be used for admin account creation"></i>
                                    </label>
                                    <input type="text" class="form-control" id="owner_name" name="owner_name" 
                                           value="{{ old('owner_name') }}" required placeholder="John Doe">
                                    @error('owner_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_email" class="form-label">
                                        Owner Email *
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="This will be your admin login email"></i>
                                    </label>
                                    <input type="email" class="form-control" id="owner_email" name="owner_email" 
                                           value="{{ old('owner_email') }}" required placeholder="owner@example.com">
                                    @error('owner_email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_phone" class="form-label">
                                        Owner Phone *
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="Your personal contact number"></i>
                                    </label>
                                    <input type="text" class="form-control" id="owner_phone" name="owner_phone" 
                                           value="{{ old('owner_phone') }}" required placeholder="+1 (555) 987-6543">
                                    @error('owner_phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="timezone" class="form-label">
                                        Timezone *
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="Your business timezone for accurate reporting"></i>
                                    </label>
                                    <select class="form-control" id="timezone" name="timezone" required>
                                        <option value="">Select Timezone</option>
                                        <option value="America/New_York" {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>Eastern Time (ET)</option>
                                        <option value="America/Chicago" {{ old('timezone') == 'America/Chicago' ? 'selected' : '' }}>Central Time (CT)</option>
                                        <option value="America/Denver" {{ old('timezone') == 'America/Denver' ? 'selected' : '' }}>Mountain Time (MT)</option>
                                        <option value="America/Los_Angeles" {{ old('timezone') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (PT)</option>
                                        <option value="Europe/London" {{ old('timezone') == 'Europe/London' ? 'selected' : '' }}>London (GMT)</option>
                                        <option value="Europe/Paris" {{ old('timezone') == 'Europe/Paris' ? 'selected' : '' }}>Paris (CET)</option>
                                        <option value="Asia/Dubai" {{ old('timezone') == 'Asia/Dubai' ? 'selected' : '' }}>Dubai (GST)</option>
                                        <option value="Asia/Karachi" {{ old('timezone') == 'Asia/Karachi' ? 'selected' : '' }}>Pakistan (PKT)</option>
                                        <option value="Asia/Kolkata" {{ old('timezone') == 'Asia/Kolkata' ? 'selected' : '' }}>India (IST)</option>
                                        <option value="Asia/Shanghai" {{ old('timezone') == 'Asia/Shanghai' ? 'selected' : '' }}>China (CST)</option>
                                        <option value="Asia/Tokyo" {{ old('timezone') == 'Asia/Tokyo' ? 'selected' : '' }}>Japan (JST)</option>
                                        <option value="Australia/Sydney" {{ old('timezone') == 'Australia/Sydney' ? 'selected' : '' }}>Sydney (AEDT)</option>
                                    </select>
                                    @error('timezone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_password" class="form-label">
                                        Admin Password *
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="Minimum 8 characters with letters and numbers"></i>
                                    </label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password" 
                                           required placeholder="Enter secure password">
                                    @error('admin_password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_password_confirmation" class="form-label">
                                        Confirm Admin Password *
                                    </label>
                                    <input type="password" class="form-control" id="admin_password_confirmation" 
                                           name="admin_password_confirmation" required placeholder="Confirm password">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3: Currency & Tax Settings -->
                    <div class="form-section" data-section="3">
                        <h3 class="section-title">
                            <i class="fas fa-money-bill-wave"></i> Currency & Tax Settings
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency" class="form-label">
                                        Currency *
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="Primary currency for your business transactions"></i>
                                    </label>
                                    <select class="form-control" id="currency" name="currency" required>
                                        <option value="">Select Currency</option>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                        <option value="PKR" {{ old('currency') == 'PKR' ? 'selected' : '' }}>PKR - Pakistani Rupee</option>
                                        <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                        <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                                        <option value="CAD" {{ old('currency') == 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                        <option value="AUD" {{ old('currency') == 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                                        <option value="JPY" {{ old('currency') == 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                                        <option value="CNY" {{ old('currency') == 'CNY' ? 'selected' : '' }}>CNY - Chinese Yuan</option>
                                    </select>
                                    @error('currency')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency_symbol" class="form-label">
                                        Currency Symbol *
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="Auto-filled based on currency selection"></i>
                                    </label>
                                    <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" 
                                           value="{{ old('currency_symbol', '$') }}" required placeholder="$">
                                    @error('currency_symbol')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tax_rate" class="form-label">
                                        Default Tax Rate (%) *
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="Standard tax rate for your products (can be changed later)"></i>
                                    </label>
                                    <input type="number" class="form-control" id="tax_rate" name="tax_rate" 
                                           value="{{ old('tax_rate', '0') }}" min="0" max="100" step="0.01" required placeholder="0.00">
                                    @error('tax_rate')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tax_number" class="form-label">
                                        Tax Registration Number
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="Your business tax registration number (optional)"></i>
                                    </label>
                                    <input type="text" class="form-control" id="tax_number" name="tax_number" 
                                           value="{{ old('tax_number') }}" placeholder="Enter tax number">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 4: POS System Preferences -->
                    <div class="form-section" data-section="4">
                        <h3 class="section-title">
                            <i class="fas fa-cogs"></i> POS System Preferences
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="preference-card">
                                    <div class="preference-title">
                                        <i class="fas fa-receipt"></i> Receipt Settings
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="receipt_format" class="form-label">Receipt Format</label>
                                        <select class="form-control" id="receipt_format" name="receipt_format">
                                            <option value="80mm" {{ old('receipt_format') == '80mm' ? 'selected' : '' }}>80mm Thermal (Standard)</option>
                                            <option value="58mm" {{ old('receipt_format') == '58mm' ? 'selected' : '' }}>58mm Thermal (Compact)</option>
                                            <option value="a4" {{ old('receipt_format') == 'a4' ? 'selected' : '' }}>A4 Paper</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="receipt_footer" class="form-label">Receipt Footer Message</label>
                                        <textarea class="form-control" id="receipt_footer" name="receipt_footer" 
                                                  rows="2" placeholder="Thank you for your business!">{{ old('receipt_footer', 'Thank you for your business!') }}</textarea>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="auto_print_receipt" 
                                               name="auto_print_receipt" value="1" {{ old('auto_print_receipt') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_print_receipt">
                                            Auto-print receipt after sale
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="preference-card">
                                    <div class="preference-title">
                                        <i class="fas fa-barcode"></i> Product & Inventory
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="barcode_format" class="form-label">Barcode Format</label>
                                        <select class="form-control" id="barcode_format" name="barcode_format">
                                            <option value="CODE128" {{ old('barcode_format') == 'CODE128' ? 'selected' : '' }}>CODE128 (Recommended)</option>
                                            <option value="CODE39" {{ old('barcode_format') == 'CODE39' ? 'selected' : '' }}>CODE39</option>
                                            <option value="EAN13" {{ old('barcode_format') == 'EAN13' ? 'selected' : '' }}>EAN13</option>
                                            <option value="EAN8" {{ old('barcode_format') == 'EAN8' ? 'selected' : '' }}>EAN8</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="low_stock_threshold" class="form-label">Low Stock Alert Threshold</label>
                                        <input type="number" class="form-control" id="low_stock_threshold" 
                                               name="low_stock_threshold" value="{{ old('low_stock_threshold', '10') }}" 
                                               min="1" max="100" placeholder="10">
                                    </div>
                                    
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="track_inventory" 
                                               name="track_inventory" value="1" {{ old('track_inventory', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="track_inventory">
                                            Enable inventory tracking
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="preference-card">
                                    <div class="preference-title">
                                        <i class="fas fa-bell"></i> Notifications
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" id="email_notifications" 
                                               name="email_notifications" value="1" {{ old('email_notifications', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_notifications">
                                            Email notifications
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" id="low_stock_alerts" 
                                               name="low_stock_alerts" value="1" {{ old('low_stock_alerts', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="low_stock_alerts">
                                            Low stock email alerts
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="daily_sales_report" 
                                               name="daily_sales_report" value="1" {{ old('daily_sales_report') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="daily_sales_report">
                                            Daily sales report email
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="preference-card">
                                    <div class="preference-title">
                                        <i class="fas fa-palette"></i> Display Settings
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="date_format" class="form-label">Date Format</label>
                                        <select class="form-control" id="date_format" name="date_format">
                                            <option value="Y-m-d" {{ old('date_format') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                            <option value="m/d/Y" {{ old('date_format') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                            <option value="d/m/Y" {{ old('date_format') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                            <option value="d-m-Y" {{ old('date_format') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="time_format" class="form-label">Time Format</label>
                                        <select class="form-control" id="time_format" name="time_format">
                                            <option value="H:i" {{ old('time_format') == 'H:i' ? 'selected' : '' }}>24 Hour (23:59)</option>
                                            <option value="h:i A" {{ old('time_format') == 'h:i A' ? 'selected' : '' }}>12 Hour (11:59 PM)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 5: Logo Upload & Finish -->
                    <div class="form-section" data-section="5">
                        <h3 class="section-title">
                            <i class="fas fa-image"></i> Shop Logo & Final Setup
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shop_logo" class="form-label">
                                        Upload Shop Logo
                                        <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" 
                                           title="This will appear on receipts and throughout the system"></i>
                                    </label>
                                    <input type="file" class="form-control" id="shop_logo" name="shop_logo" 
                                           accept="image/jpeg,image/png,image/jpg,image/gif">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> 
                                        Recommended: 200x200 pixels, Max size: 2MB, Format: JPG, PNG, GIF
                                    </div>
                                    @error('shop_logo')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div id="logo-preview" class="text-center mt-3" style="display: none;">
                                    <img id="logo-image" class="logo-preview" alt="Logo Preview">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="preference-card">
                                    <div class="preference-title">
                                        <i class="fas fa-check-circle"></i> Setup Summary
                                    </div>
                                    
                                    <div class="mb-2">
                                        <strong>Business:</strong> <span id="summary-business">-</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Owner:</strong> <span id="summary-owner">-</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Currency:</strong> <span id="summary-currency">-</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Timezone:</strong> <span id="summary-timezone">-</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Tax Rate:</strong> <span id="summary-tax">-</span>
                                    </div>
                                    
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Ready to complete setup!</strong><br>
                                        Your POS system will be configured with these settings.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                            <i class="fas fa-check"></i> Complete Setup
                        </button>
                    </div>
                </form>
                
                <div class="developer-credit">
                    <strong>{{ App\Helpers\BrandHelper::getCompleteBranding() }}</strong><br>
                    Professional POS Solutions for Your Business
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 5;
        
        // Initialize tooltips
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => {
            new bootstrap.Tooltip(tooltip);
        });
        
        function changeStep(direction) {
            const currentSection = document.querySelector(`.form-section[data-section="${currentStep}"]`);
            
            if (direction === 1) {
                // Validate current step before proceeding
                if (!validateStep(currentStep)) {
                    return;
                }
            }
            
            // Hide current step
            currentSection.classList.remove('active');
            document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
            
            // Mark as completed if moving forward
            if (direction === 1) {
                document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('completed');
            }
            
            // Update step
            currentStep += direction;
            
            // Show new step
            document.querySelector(`.form-section[data-section="${currentStep}"]`).classList.add('active');
            document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
            
            // Update progress bar
            updateProgressBar();
            
            // Update button visibility
            updateButtons();
            
            // Update summary if on final step
            if (currentStep === totalSteps) {
                updateSummary();
            }
        }
        
        function updateProgressBar() {
            const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
            document.getElementById('stepProgress').style.width = progress + '%';
        }
        
        function updateButtons() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            
            prevBtn.style.display = currentStep === 1 ? 'none' : 'inline-block';
            nextBtn.style.display = currentStep === totalSteps ? 'none' : 'inline-block';
            submitBtn.style.display = currentStep === totalSteps ? 'inline-block' : 'none';
        }
        
        function validateStep(step) {
            const section = document.querySelector(`.form-section[data-section="${step}"]`);
            const requiredFields = section.querySelectorAll('[required]');
            let isValid = true;
            
            for (let field of requiredFields) {
                field.classList.remove('is-invalid');
                
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    if (isValid) {
                        field.focus();
                        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    isValid = false;
                }
            }
            
            // Additional validation for password confirmation
            if (step === 2) {
                const password = document.getElementById('admin_password').value;
                const confirmPassword = document.getElementById('admin_password_confirmation').value;
                
                if (password !== confirmPassword) {
                    document.getElementById('admin_password_confirmation').classList.add('is-invalid');
                    if (isValid) {
                        document.getElementById('admin_password_confirmation').focus();
                    }
                    isValid = false;
                }
            }
            
            return isValid;
        }
        
        function updateSummary() {
            document.getElementById('summary-business').textContent = 
                document.getElementById('shop_name').value || '-';
            document.getElementById('summary-owner').textContent = 
                document.getElementById('owner_name').value || '-';
            document.getElementById('summary-currency').textContent = 
                document.getElementById('currency').value || '-';
            document.getElementById('summary-timezone').textContent = 
                document.getElementById('timezone').options[document.getElementById('timezone').selectedIndex].text || '-';
            document.getElementById('summary-tax').textContent = 
                document.getElementById('tax_rate').value + '%' || '-';
        }
        
        // Logo preview
        document.getElementById('shop_logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logo-image').src = e.target.result;
                    document.getElementById('logo-preview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Currency symbol auto-update
        document.getElementById('currency').addEventListener('change', function() {
            const symbols = {
                'USD': '$', 'EUR': '€', 'GBP': '£', 'PKR': '₨', 'INR': '₹', 
                'AED': 'د.إ', 'CAD': '$', 'AUD': '$', 'JPY': '¥', 'CNY': '¥'
            };
            document.getElementById('currency_symbol').value = symbols[this.value] || '$';
        });
        
        // Form validation
        document.getElementById('setupForm').addEventListener('submit', function(e) {
            if (!validateStep(currentStep)) {
                e.preventDefault();
            } else {
                // Show loading state
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Setting up...';
                submitBtn.disabled = true;
            }
        });
        
        // Initialize progress bar
        updateProgressBar();
    </script>
</body>
</html> 