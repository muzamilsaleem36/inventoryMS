@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Expense: {{ $expense->title }}
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Expense Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">
                                                        Expense Title <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $expense->title) }}" required>
                                                    @error('title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="reference_number" class="form-label">Reference Number</label>
                                                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number" name="reference_number" value="{{ old('reference_number', $expense->reference_number) }}" placeholder="Receipt/Invoice number">
                                                    @error('reference_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Enter expense description">{{ old('description', $expense->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="category" class="form-label">
                                                        Category <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                                        <option value="">Select category</option>
                                                        <option value="office_supplies" {{ old('category', $expense->category) == 'office_supplies' ? 'selected' : '' }}>Office Supplies</option>
                                                        <option value="utilities" {{ old('category', $expense->category) == 'utilities' ? 'selected' : '' }}>Utilities</option>
                                                        <option value="rent" {{ old('category', $expense->category) == 'rent' ? 'selected' : '' }}>Rent</option>
                                                        <option value="marketing" {{ old('category', $expense->category) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                                        <option value="travel" {{ old('category', $expense->category) == 'travel' ? 'selected' : '' }}>Travel</option>
                                                        <option value="meals" {{ old('category', $expense->category) == 'meals' ? 'selected' : '' }}>Meals</option>
                                                        <option value="equipment" {{ old('category', $expense->category) == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                                        <option value="maintenance" {{ old('category', $expense->category) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                                        <option value="insurance" {{ old('category', $expense->category) == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                                        <option value="professional_services" {{ old('category', $expense->category) == 'professional_services' ? 'selected' : '' }}>Professional Services</option>
                                                        <option value="other" {{ old('category', $expense->category) == 'other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                    @error('category')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="amount" class="form-label">
                                                        Amount <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $expense->amount) }}" step="0.01" min="0" required>
                                                    </div>
                                                    @error('amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="expense_date" class="form-label">
                                                        Expense Date <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                                                    @error('expense_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="payment_method" class="form-label">Payment Method</label>
                                                    <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                                                        <option value="">Select payment method</option>
                                                        <option value="cash" {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                                        <option value="credit_card" {{ old('payment_method', $expense->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                        <option value="debit_card" {{ old('payment_method', $expense->payment_method) == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                                        <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                        <option value="check" {{ old('payment_method', $expense->payment_method) == 'check' ? 'selected' : '' }}>Check</option>
                                                        <option value="digital_wallet" {{ old('payment_method', $expense->payment_method) == 'digital_wallet' ? 'selected' : '' }}>Digital Wallet</option>
                                                        <option value="other" {{ old('payment_method', $expense->payment_method) == 'other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                    @error('payment_method')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="vendor" class="form-label">Vendor/Supplier</label>
                                                    <input type="text" class="form-control @error('vendor') is-invalid @enderror" id="vendor" name="vendor" value="{{ old('vendor', $expense->vendor) }}" placeholder="Enter vendor name">
                                                    @error('vendor')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="tax_amount" class="form-label">Tax Amount</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control @error('tax_amount') is-invalid @enderror" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', $expense->tax_amount) }}" step="0.01" min="0">
                                                    </div>
                                                    @error('tax_amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Additional Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Enter any additional notes">{{ old('notes', $expense->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Receipt/Attachment</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($expense->receipt)
                                            <div class="current-receipt mb-3">
                                                <label class="form-label">Current Receipt</label>
                                                <div class="text-center">
                                                    @if(in_array(pathinfo($expense->receipt, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                                        <img src="{{ asset('storage/' . $expense->receipt) }}" alt="Receipt" class="img-fluid rounded" style="max-height: 200px;">
                                                    @else
                                                        <div class="bg-secondary rounded p-3">
                                                            <i class="fas fa-file-pdf fa-3x text-white"></i>
                                                            <p class="text-white mt-2">PDF Receipt</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="text-center mt-2">
                                                    <a href="{{ asset('storage/' . $expense->receipt) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="mb-3">
                                            <label for="receipt" class="form-label">
                                                {{ $expense->receipt ? 'Change Receipt' : 'Upload Receipt' }}
                                            </label>
                                            <input type="file" class="form-control @error('receipt') is-invalid @enderror" id="receipt" name="receipt" accept="image/*,application/pdf">
                                            @error('receipt')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Supported formats: JPG, PNG, PDF. Max size: 5MB.
                                            </small>
                                        </div>
                                        
                                        <div class="image-preview mt-3 text-center" style="display: none;">
                                            <label class="form-label">Preview</label>
                                            <div>
                                                <img id="imagePreview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                            </div>
                                        </div>

                                        @if($expense->receipt)
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="remove_receipt" name="remove_receipt" value="1">
                                                    <label class="form-check-label" for="remove_receipt">
                                                        Remove current receipt
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Approval Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">
                                                Status <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="">Select status</option>
                                                <option value="pending" {{ old('status', $expense->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                @can('expense-approve')
                                                    <option value="approved" {{ old('status', $expense->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="rejected" {{ old('status', $expense->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                @endcan
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_reimbursable" name="is_reimbursable" value="1" {{ old('is_reimbursable', $expense->is_reimbursable) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_reimbursable">
                                                    Reimbursable expense
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_billable" name="is_billable" value="1" {{ old('is_billable', $expense->is_billable) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_billable">
                                                    Billable to customer
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Store Assignment</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="store_id" class="form-label">Store</label>
                                            <select class="form-select @error('store_id') is-invalid @enderror" id="store_id" name="store_id">
                                                <option value="">Select store (optional)</option>
                                                @foreach($stores as $store)
                                                    <option value="{{ $store->id }}" {{ old('store_id', $expense->store_id) == $store->id ? 'selected' : '' }}>
                                                        {{ $store->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('store_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Expense Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Created by:</strong> {{ $expense->user->name ?? 'Unknown' }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Created:</strong> {{ $expense->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Updated:</strong> {{ $expense->updated_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Expense
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Image preview
    document.getElementById('receipt').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const fileType = file.type;
            if (fileType.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.querySelector('.image-preview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.querySelector('.image-preview').style.display = 'none';
            }
        }
    });

    // Calculate total if tax is added
    document.getElementById('tax_amount').addEventListener('input', function() {
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const tax = parseFloat(this.value) || 0;
        const total = amount + tax;
        
        // You can add a total display field if needed
        // document.getElementById('total_amount').textContent = total.toFixed(2);
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const category = document.getElementById('category').value;
        const amount = document.getElementById('amount').value;
        const expenseDate = document.getElementById('expense_date').value;
        const status = document.getElementById('status').value;
        
        if (!title || !category || !amount || !expenseDate || !status) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return;
        }
        
        if (parseFloat(amount) <= 0) {
            e.preventDefault();
            alert('Amount must be greater than 0.');
            document.getElementById('amount').focus();
            return;
        }
    });
</script>
@endpush 