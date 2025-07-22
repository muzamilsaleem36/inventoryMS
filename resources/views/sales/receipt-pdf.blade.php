<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->sale_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .info-column {
            width: 48%;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .total-final {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #333;
            padding-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
        }
        
        .notes {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
        }
        
        hr {
            border: none;
            height: 1px;
            background-color: #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Business Header -->
    <div class="header">
        <h1>{{ $settings['business_name'] ?? 'Your Business Name' }}</h1>
        <p>{{ $settings['business_address'] ?? 'Your Business Address' }}</p>
        <p>{{ $settings['business_phone'] ?? 'Phone' }} | {{ $settings['business_email'] ?? 'Email' }}</p>
    </div>
    
    <hr>
    
    <!-- Sale Information -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-column">
                <strong>Receipt #:</strong> {{ $sale->sale_number }}<br>
                <strong>Date:</strong> {{ $sale->created_at->format('M d, Y h:i A') }}<br>
                <strong>Cashier:</strong> {{ $sale->user->name }}
            </div>
            <div class="info-column">
                <strong>Customer:</strong> 
                @if($sale->customer)
                    {{ $sale->customer->name }}<br>
                    @if($sale->customer->phone)
                        <small>{{ $sale->customer->phone }}</small>
                    @endif
                @else
                    Walk-in Customer
                @endif
            </div>
        </div>
    </div>
    
    <!-- Items -->
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">${{ number_format($item->price, 2) }}</td>
                <td class="text-right">${{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Totals -->
    <div class="total-section">
        <div class="total-row">
            <span><strong>Subtotal:</strong></span>
            <span>${{ number_format($sale->subtotal, 2) }}</span>
        </div>
        
        @if($sale->discount_amount > 0)
        <div class="total-row">
            <span><strong>Discount ({{ $sale->discount_type == 'percentage' ? $sale->discount_value.'%' : '$'.number_format($sale->discount_value, 2) }}):</strong></span>
            <span>-${{ number_format($sale->discount_amount, 2) }}</span>
        </div>
        @endif
        
        @if($sale->tax_amount > 0)
        <div class="total-row">
            <span><strong>Tax ({{ $sale->tax_rate }}%):</strong></span>
            <span>${{ number_format($sale->tax_amount, 2) }}</span>
        </div>
        @endif
        
        <div class="total-row total-final">
            <span><strong>Total:</strong></span>
            <span><strong>${{ number_format($sale->total, 2) }}</strong></span>
        </div>
        
        <div class="total-row">
            <span><strong>Payment Method:</strong></span>
            <span>{{ ucfirst($sale->payment_method) }}</span>
        </div>
    </div>
    
    @if($sale->notes)
    <div class="notes">
        <strong>Notes:</strong><br>
        {{ $sale->notes }}
    </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>{{ $settings['receipt_footer'] ?? 'Visit us again soon!' }}</p>
    </div>
</body>
</html> 