@extends('layouts.app')

@section('title', 'Point of Sale')

@section('styles')
<style>
    .pos-container {
        height: calc(100vh - 200px);
        overflow: hidden;
    }
    
    .product-grid {
        height: 100%;
        overflow-y: auto;
        background: #f8f9fa;
        border-radius: 15px;
        padding: 1rem;
    }
    
    .product-card {
        background: white;
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 1rem;
    }
    
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .product-image {
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px 10px 0 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
    }
    
    .cart-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .cart-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border-radius: 15px 15px 0 0;
        text-align: center;
    }
    
    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        max-height: 300px;
    }
    
    .cart-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .cart-summary {
        background: #f8f9fa;
        padding: 1rem;
        border-top: 2px solid #e9ecef;
    }
    
    .cart-total {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 1rem;
        text-align: center;
        font-size: 1.25rem;
        font-weight: bold;
    }
    
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .quantity-btn {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 50%;
        background: #667eea;
        color: white;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    
    .search-container {
        margin-bottom: 1rem;
    }
    
    .payment-modal .modal-dialog {
        max-width: 500px;
    }
    
    .payment-method {
        background: #f8f9fa;
        border: 2px solid transparent;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .payment-method:hover {
        border-color: #667eea;
    }
    
    .payment-method.active {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.1);
    }
    
    .barcode-scanner {
        background: #fff3cd;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">
        <i class="fas fa-cash-register text-primary-gradient"></i> Point of Sale
    </h1>
    <div>
        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#barcodeModal">
            <i class="fas fa-barcode"></i> Scan Barcode
        </button>
        <button type="button" class="btn btn-secondary" onclick="clearCart()">
            <i class="fas fa-trash"></i> Clear Cart
        </button>
    </div>
</div>

<div class="row pos-container">
    <!-- Products Section -->
    <div class="col-lg-8">
        <div class="product-grid">
            <!-- Search Bar -->
            <div class="search-container">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="productSearch" class="form-control" placeholder="Search products...">
                </div>
            </div>
            
            <!-- Category Filter -->
            <div class="mb-3">
                <select id="categoryFilter" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Products Grid -->
            <div id="productsGrid" class="row">
                @forelse($products ?? [] as $product)
                <div class="col-md-4 col-sm-6 mb-3 product-item" 
                     data-category="{{ $product->category_id }}" 
                     data-name="{{ strtolower($product->name) }}"
                     data-barcode="{{ $product->barcode }}">
                    <div class="product-card" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->selling_price }}, {{ $product->stock_quantity }})">
                        <div class="product-image">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <i class="fas fa-box"></i>
                            @endif
                        </div>
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1 text-truncate">{{ $product->name }}</h6>
                            <p class="card-text mb-1">
                                <strong>{{ App\Helpers\BrandHelper::formatCurrency($product->selling_price) }}</strong>
                            </p>
                            <small class="text-muted">Stock: {{ $product->stock_quantity }}</small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No products available</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Cart Section -->
    <div class="col-lg-4">
        <div class="cart-container">
            <div class="cart-header">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart"></i> Shopping Cart
                </h5>
            </div>
            
            <div class="cart-items" id="cartItems">
                <p class="text-center text-muted">Cart is empty</p>
            </div>
            
            <div class="cart-summary">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span id="subtotal">{{ App\Helpers\BrandHelper::formatCurrency(0) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax ({{ App\Helpers\BrandHelper::getTaxRate() }}%):</span>
                    <span id="tax">{{ App\Helpers\BrandHelper::formatCurrency(0) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount:</span>
                    <span id="discount">{{ App\Helpers\BrandHelper::formatCurrency(0) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <input type="number" id="discountInput" class="form-control form-control-sm me-2" placeholder="Discount %" min="0" max="100" onchange="updateCart()">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#customerModal">
                        <i class="fas fa-user"></i> Customer
                    </button>
                </div>
            </div>
            
            <div class="cart-total" id="total">
                Total: {{ App\Helpers\BrandHelper::formatCurrency(0) }}
            </div>
            
            <div class="p-3">
                <button type="button" class="btn btn-success btn-lg w-100" onclick="checkout()" id="checkoutBtn" disabled>
                    <i class="fas fa-credit-card"></i> Checkout
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="customerSearch" placeholder="Search customers...">
                </div>
                <div class="list-group" id="customerList">
                    <div class="list-group-item active" data-id="" onclick="selectCustomer(null, 'Walk-in Customer')">
                        <strong>Walk-in Customer</strong>
                    </div>
                    @foreach($customers ?? [] as $customer)
                    <div class="list-group-item" data-id="{{ $customer->id }}" onclick="selectCustomer({{ $customer->id }}, '{{ $customer->name }}')">
                        <strong>{{ $customer->name }}</strong><br>
                        <small class="text-muted">{{ $customer->email }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade payment-modal" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <h3 class="text-primary" id="finalTotal">{{ App\Helpers\BrandHelper::formatCurrency(0) }}</h3>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <div class="payment-method active" data-method="cash" onclick="selectPaymentMethod('cash')">
                        <i class="fas fa-money-bill-wave"></i> Cash
                    </div>
                    <div class="payment-method" data-method="card" onclick="selectPaymentMethod('card')">
                        <i class="fas fa-credit-card"></i> Credit/Debit Card
                    </div>
                    <div class="payment-method" data-method="bank" onclick="selectPaymentMethod('bank')">
                        <i class="fas fa-university"></i> Bank Transfer
                    </div>
                </div>
                
                <div class="mb-3" id="cashPayment">
                    <label class="form-label">Amount Received</label>
                    <input type="number" class="form-control" id="amountReceived" step="0.01" onchange="calculateChange()">
                    <div class="mt-2">
                        <strong>Change: <span id="change">{{ App\Helpers\BrandHelper::formatCurrency(0) }}</span></strong>
                    </div>
                </div>
                
                <div class="mb-3 d-none" id="cardPayment">
                    <label class="form-label">Card Reference</label>
                    <input type="text" class="form-control" id="cardReference" placeholder="Transaction reference">
                </div>
                
                <div class="mb-3 d-none" id="bankPayment">
                    <label class="form-label">Bank Reference</label>
                    <input type="text" class="form-control" id="bankReference" placeholder="Bank transaction reference">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="completeSale()">
                    <i class="fas fa-check"></i> Complete Sale
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Barcode Scanner Modal -->
<div class="modal fade" id="barcodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Barcode Scanner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="barcode-scanner">
                    <i class="fas fa-barcode fa-3x text-warning"></i>
                    <h5 class="mt-2">Scan or Enter Barcode</h5>
                    <input type="text" class="form-control mt-3" id="barcodeInput" placeholder="Enter barcode or scan">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addByBarcode()">Add Product</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let cart = [];
    let selectedCustomer = null;
    let selectedPaymentMethod = 'cash';
    const taxRate = {{ App\Helpers\BrandHelper::getTaxRate() }} / 100;
    const currencySymbol = '{{ App\Helpers\BrandHelper::getCurrencySymbol() }}';
    
    function addToCart(id, name, price, stock) {
        if (stock <= 0) {
            alert('Product is out of stock!');
            return;
        }
        
        const existingItem = cart.find(item => item.id === id);
        
        if (existingItem) {
            if (existingItem.quantity >= stock) {
                alert('Cannot add more. Insufficient stock!');
                return;
            }
            existingItem.quantity++;
        } else {
            cart.push({
                id: id,
                name: name,
                price: price,
                quantity: 1,
                maxStock: stock
            });
        }
        
        updateCart();
    }
    
    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        updateCart();
    }
    
    function updateQuantity(id, quantity) {
        const item = cart.find(item => item.id === id);
        if (item) {
            if (quantity <= 0) {
                removeFromCart(id);
                return;
            }
            if (quantity > item.maxStock) {
                alert('Cannot exceed available stock!');
                return;
            }
            item.quantity = quantity;
            updateCart();
        }
    }
    
    function updateCart() {
        const cartItems = document.getElementById('cartItems');
        const subtotalEl = document.getElementById('subtotal');
        const taxEl = document.getElementById('tax');
        const discountEl = document.getElementById('discount');
        const totalEl = document.getElementById('total');
        const checkoutBtn = document.getElementById('checkoutBtn');
        
        if (cart.length === 0) {
            cartItems.innerHTML = '<p class="text-center text-muted">Cart is empty</p>';
            checkoutBtn.disabled = true;
        } else {
            cartItems.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${item.name}</h6>
                        <small class="text-muted">${currencySymbol}${item.price.toFixed(2)} each</small>
                    </div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                        <span class="mx-2">${item.quantity}</span>
                        <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                    </div>
                    <div class="text-end ms-2">
                        <div class="fw-bold">${currencySymbol}${(item.price * item.quantity).toFixed(2)}</div>
                        <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
            checkoutBtn.disabled = false;
        }
        
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discountPercent = parseFloat(document.getElementById('discountInput').value || 0);
        const discountAmount = subtotal * (discountPercent / 100);
        const taxableAmount = subtotal - discountAmount;
        const tax = taxableAmount * taxRate;
        const total = taxableAmount + tax;
        
        subtotalEl.textContent = `${currencySymbol}${subtotal.toFixed(2)}`;
        discountEl.textContent = `${currencySymbol}${discountAmount.toFixed(2)}`;
        taxEl.textContent = `${currencySymbol}${tax.toFixed(2)}`;
        totalEl.textContent = `Total: ${currencySymbol}${total.toFixed(2)}`;
        
        document.getElementById('finalTotal').textContent = `${currencySymbol}${total.toFixed(2)}`;
    }
    
    function clearCart() {
        if (confirm('Are you sure you want to clear the cart?')) {
            cart = [];
            updateCart();
        }
    }
    
    function selectCustomer(id, name) {
        selectedCustomer = { id: id, name: name };
        document.querySelectorAll('#customerList .list-group-item').forEach(item => {
            item.classList.remove('active');
        });
        event.target.closest('.list-group-item').classList.add('active');
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('customerModal'));
        modal.hide();
    }
    
    function selectPaymentMethod(method) {
        selectedPaymentMethod = method;
        document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('active'));
        document.querySelector(`[data-method="${method}"]`).classList.add('active');
        
        // Show/hide payment specific fields
        document.getElementById('cashPayment').classList.toggle('d-none', method !== 'cash');
        document.getElementById('cardPayment').classList.toggle('d-none', method !== 'card');
        document.getElementById('bankPayment').classList.toggle('d-none', method !== 'bank');
    }
    
    function calculateChange() {
        const total = parseFloat(document.getElementById('finalTotal').textContent.replace(currencySymbol, ''));
        const received = parseFloat(document.getElementById('amountReceived').value || 0);
        const change = Math.max(0, received - total);
        document.getElementById('change').textContent = `${currencySymbol}${change.toFixed(2)}`;
    }
    
    function checkout() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }
    
    function completeSale() {
        const total = parseFloat(document.getElementById('finalTotal').textContent.replace(currencySymbol, ''));
        
        if (selectedPaymentMethod === 'cash') {
            const received = parseFloat(document.getElementById('amountReceived').value || 0);
            if (received < total) {
                alert('Insufficient payment amount!');
                return;
            }
        }
        
        // Prepare sale data
        const saleData = {
            customer_id: selectedCustomer?.id || null,
            items: cart,
            subtotal: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
            discount_percent: parseFloat(document.getElementById('discountInput').value || 0),
            tax_rate: taxRate * 100,
            total_amount: total,
            payment_method: selectedPaymentMethod,
            payment_reference: getPaymentReference(),
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        
        // Submit sale
        fetch('{{ route("sales.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': saleData._token
            },
            body: JSON.stringify(saleData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Sale completed successfully!');
                
                // Open receipt in new window
                window.open(`/sales/${data.sale_id}/receipt`, '_blank');
                
                // Reset POS
                cart = [];
                selectedCustomer = null;
                document.getElementById('discountInput').value = '';
                document.getElementById('amountReceived').value = '';
                updateCart();
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                modal.hide();
            } else {
                alert('Error: ' + (data.message || 'Sale failed'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the sale');
        });
    }
    
    function getPaymentReference() {
        switch (selectedPaymentMethod) {
            case 'card':
                return document.getElementById('cardReference').value;
            case 'bank':
                return document.getElementById('bankReference').value;
            default:
                return null;
        }
    }
    
    function addByBarcode() {
        const barcode = document.getElementById('barcodeInput').value;
        if (!barcode) {
            alert('Please enter a barcode');
            return;
        }
        
        const productEl = document.querySelector(`[data-barcode="${barcode}"]`);
        if (productEl) {
            productEl.querySelector('.product-card').click();
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeModal'));
            modal.hide();
            document.getElementById('barcodeInput').value = '';
        } else {
            alert('Product not found with this barcode');
        }
    }
    
    // Search functionality
    document.getElementById('productSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const products = document.querySelectorAll('.product-item');
        
        products.forEach(product => {
            const name = product.dataset.name;
            const visible = name.includes(searchTerm);
            product.style.display = visible ? 'block' : 'none';
        });
    });
    
    // Category filter
    document.getElementById('categoryFilter').addEventListener('change', function() {
        const categoryId = this.value;
        const products = document.querySelectorAll('.product-item');
        
        products.forEach(product => {
            const productCategory = product.dataset.category;
            const visible = !categoryId || productCategory === categoryId;
            product.style.display = visible ? 'block' : 'none';
        });
    });
    
    // Customer search
    document.getElementById('customerSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const customers = document.querySelectorAll('#customerList .list-group-item');
        
        customers.forEach(customer => {
            const text = customer.textContent.toLowerCase();
            const visible = text.includes(searchTerm);
            customer.style.display = visible ? 'block' : 'none';
        });
    });
    
    // Barcode input focus when modal opens
    document.getElementById('barcodeModal').addEventListener('shown.bs.modal', function() {
        document.getElementById('barcodeInput').focus();
    });
    
    // Initialize
    updateCart();
</script>
@endsection 