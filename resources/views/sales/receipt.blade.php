<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->invoice_number }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .shop-logo {
            max-width: 80px;
            max-height: 80px;
            margin-bottom: 10px;
        }
        
        .shop-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .shop-info {
            font-size: 10px;
            line-height: 1.2;
        }
        
        .receipt-info {
            margin-bottom: 15px;
            border-bottom: 1px dashed #333;
            padding-bottom: 10px;
        }
        
        .receipt-info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .items-table {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .items-header {
            border-bottom: 1px solid #333;
            font-weight: bold;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            flex-wrap: wrap;
        }
        
        .item-name {
            flex: 1;
            min-width: 120px;
        }
        
        .item-qty-price {
            text-align: right;
            min-width: 80px;
        }
        
        .item-total {
            text-align: right;
            font-weight: bold;
            min-width: 60px;
        }
        
        .totals {
            border-top: 1px solid #333;
            border-bottom: 2px solid #333;
            padding: 10px 0;
            margin-bottom: 15px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px dashed #333;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .payment-info {
            margin-bottom: 15px;
            border-bottom: 1px dashed #333;
            padding-bottom: 10px;
        }
        
        .footer {
            text-align: center;
            font-size: 10px;
            line-height: 1.3;
        }
        
        .branding {
            border-top: 1px dashed #333;
            padding-top: 10px;
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .print-buttons {
            text-align: center;
            margin: 20px 0;
        }
        
        .btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #5a67d8;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
    </style>
</head>
<body>
    <div class="print-buttons no-print">
        <button class="btn" onclick="window.print()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Sales
        </a>
    </div>
    
    <div class="receipt">
        <!-- Header -->
        <div class="receipt-header">
            @if(App\Helpers\BrandHelper::getShopLogo())
                <img src="{{ asset('storage/' . App\Helpers\BrandHelper::getShopLogo()) }}" alt="Logo" class="shop-logo">
            @endif
            <div class="shop-name">{{ App\Helpers\BrandHelper::getShopName() }}</div>
            <div class="shop-info">
                @php $contact = App\Helpers\BrandHelper::getShopContact(); @endphp
                {{ $contact['address'] }}<br>
                Phone: {{ $contact['phone'] }}<br>
                Email: {{ $contact['email'] }}<br>
                @if($contact['website'])
                    Website: {{ $contact['website'] }}
                @endif
            </div>
        </div>
        
        <!-- Receipt Info -->
        <div class="receipt-info">
            <div>
                <span>Receipt #:</span>
                <span>{{ $sale->invoice_number }}</span>
            </div>
            <div>
                <span>Date:</span>
                <span>{{ $sale->created_at->format('M j, Y H:i') }}</span>
            </div>
            <div>
                <span>Cashier:</span>
                <span>{{ $sale->user->name }}</span>
            </div>
            <div>
                <span>Customer:</span>
                <span>{{ $sale->customer->name ?? 'Walk-in Customer' }}</span>
            </div>
        </div>
        
        <!-- Items -->
        <div class="items-table">
            <div class="items-header">
                <span>Item</span>
                <span>Qty x Price</span>
                <span>Total</span>
            </div>
            
            @foreach($sale->items as $item)
            <div class="item-row">
                <div class="item-name">{{ $item->product->name }}</div>
                <div class="item-qty-price">{{ $item->quantity }} x {{ App\Helpers\BrandHelper::formatCurrency($item->unit_price) }}</div>
                <div class="item-total">{{ App\Helpers\BrandHelper::formatCurrency($item->total_price) }}</div>
            </div>
            @endforeach
        </div>
        
        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>{{ App\Helpers\BrandHelper::formatCurrency($sale->subtotal) }}</span>
            </div>
            
            @if($sale->discount_amount > 0)
            <div class="total-row">
                <span>Discount ({{ $sale->discount_percent }}%):</span>
                <span>-{{ App\Helpers\BrandHelper::formatCurrency($sale->discount_amount) }}</span>
            </div>
            @endif
            
            @if($sale->tax_amount > 0)
            <div class="total-row">
                <span>Tax ({{ $sale->tax_rate }}%):</span>
                <span>{{ App\Helpers\BrandHelper::formatCurrency($sale->tax_amount) }}</span>
            </div>
            @endif
            
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>{{ App\Helpers\BrandHelper::formatCurrency($sale->total_amount) }}</span>
            </div>
        </div>
        
        <!-- Payment Info -->
        <div class="payment-info">
            <div class="total-row">
                <span>Payment Method:</span>
                <span>{{ ucfirst($sale->payment_method) }}</span>
            </div>
            
            @if($sale->payment_method === 'cash')
                <div class="total-row">
                    <span>Amount Paid:</span>
                    <span>{{ App\Helpers\BrandHelper::formatCurrency($sale->amount_paid ?? $sale->total_amount) }}</span>
                </div>
                @if(($sale->amount_paid ?? $sale->total_amount) > $sale->total_amount)
                <div class="total-row">
                    <span>Change:</span>
                    <span>{{ App\Helpers\BrandHelper::formatCurrency(($sale->amount_paid ?? $sale->total_amount) - $sale->total_amount) }}</span>
                </div>
                @endif
            @endif
            
            @if($sale->payment_reference)
            <div class="total-row">
                <span>Reference:</span>
                <span>{{ $sale->payment_reference }}</span>
            </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            {{ App\Helpers\BrandHelper::getReceiptFooter() }}
        </div>
        
        <!-- Branding -->
        <div class="branding">
            {{ App\Helpers\BrandHelper::getCompleteBranding() }}<br>
            Professional POS Solution
        </div>
    </div>
    
    <script>
        // Auto-print if requested
        if (window.location.search.includes('print=1')) {
            window.print();
        }
    </script>
</body>
</html> 