@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-barcode me-2"></i>
                        Generate Barcode
                    </h3>
                    <a href="{{ route('barcodes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('barcodes.store') }}" method="POST" id="barcode-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Barcode Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="type" class="form-label">
                                                        Barcode Type <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                                        <option value="">Select barcode type</option>
                                                        <option value="CODE128" {{ old('type') == 'CODE128' ? 'selected' : '' }}>CODE128</option>
                                                        <option value="CODE39" {{ old('type') == 'CODE39' ? 'selected' : '' }}>CODE39</option>
                                                        <option value="EAN13" {{ old('type') == 'EAN13' ? 'selected' : '' }}>EAN13</option>
                                                        <option value="UPC" {{ old('type') == 'UPC' ? 'selected' : '' }}>UPC</option>
                                                        <option value="QR_CODE" {{ old('type') == 'QR_CODE' ? 'selected' : '' }}>QR Code</option>
                                                    </select>
                                                    @error('type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="generation_mode" class="form-label">
                                                        Generation Mode <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select @error('generation_mode') is-invalid @enderror" id="generation_mode" name="generation_mode" required>
                                                        <option value="">Select mode</option>
                                                        <option value="single" {{ old('generation_mode') == 'single' ? 'selected' : '' }}>Single Product</option>
                                                        <option value="multiple" {{ old('generation_mode') == 'multiple' ? 'selected' : '' }}>Multiple Products</option>
                                                        <option value="custom" {{ old('generation_mode') == 'custom' ? 'selected' : '' }}>Custom Code</option>
                                                    </select>
                                                    @error('generation_mode')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Single Product Mode -->
                                        <div id="single-mode" class="generation-mode" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="product_id" class="form-label">
                                                            Select Product <span class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id">
                                                            <option value="">Select product</option>
                                                            @foreach($products as $product)
                                                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                                    {{ $product->name }} ({{ $product->sku }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('product_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="quantity" class="form-label">Quantity</label>
                                                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="1000">
                                                        @error('quantity')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Multiple Products Mode -->
                                        <div id="multiple-mode" class="generation-mode" style="display: none;">
                                            <div class="mb-3">
                                                <label class="form-label">Select Products</label>
                                                <div class="row">
                                                    @foreach($products as $product)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="products[]" value="{{ $product->id }}" id="product_{{ $product->id }}">
                                                                <label class="form-check-label" for="product_{{ $product->id }}">
                                                                    {{ $product->name }} ({{ $product->sku }})
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Custom Code Mode -->
                                        <div id="custom-mode" class="generation-mode" style="display: none;">
                                            <div class="mb-3">
                                                <label for="custom_code" class="form-label">
                                                    Custom Code <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control @error('custom_code') is-invalid @enderror" id="custom_code" name="custom_code" value="{{ old('custom_code') }}" placeholder="Enter custom code">
                                                @error('custom_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Enter the text/numbers to be encoded in the barcode.
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Barcode Configuration -->
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Barcode Configuration</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="width" class="form-label">Width (px)</label>
                                                            <input type="number" class="form-control @error('width') is-invalid @enderror" id="width" name="width" value="{{ old('width', 200) }}" min="50" max="1000">
                                                            @error('width')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="height" class="form-label">Height (px)</label>
                                                            <input type="number" class="form-control @error('height') is-invalid @enderror" id="height" name="height" value="{{ old('height', 100) }}" min="20" max="500">
                                                            @error('height')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="show_text" name="show_text" value="1" {{ old('show_text', true) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="show_text">
                                                                    Show text below barcode
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="include_product_name" name="include_product_name" value="1" {{ old('include_product_name') ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="include_product_name">
                                                                    Include product name
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Barcode Preview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="barcode-preview" class="text-center p-4">
                                            <div class="bg-light rounded p-4">
                                                <i class="fas fa-barcode fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Preview will appear here</p>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-center">
                                            <button type="button" id="generate-preview" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> Generate Preview
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Barcode Types Info</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <strong>CODE128:</strong>
                                            <p class="small text-muted">High-density barcode, supports alphanumeric characters</p>
                                        </div>
                                        <div class="mb-3">
                                            <strong>CODE39:</strong>
                                            <p class="small text-muted">Widely used, supports uppercase letters and numbers</p>
                                        </div>
                                        <div class="mb-3">
                                            <strong>EAN13:</strong>
                                            <p class="small text-muted">13-digit European Article Number</p>
                                        </div>
                                        <div class="mb-3">
                                            <strong>UPC:</strong>
                                            <p class="small text-muted">Universal Product Code, 12 digits</p>
                                        </div>
                                        <div class="mb-3">
                                            <strong>QR Code:</strong>
                                            <p class="small text-muted">2D barcode, can store URLs, text, and more</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('barcodes.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-barcode"></i> Generate Barcode
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
    // Show/hide generation modes
    document.getElementById('generation_mode').addEventListener('change', function() {
        const modes = document.querySelectorAll('.generation-mode');
        modes.forEach(mode => mode.style.display = 'none');
        
        const selectedMode = this.value;
        if (selectedMode) {
            document.getElementById(selectedMode + '-mode').style.display = 'block';
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const generationMode = document.getElementById('generation_mode').value;
        if (generationMode) {
            document.getElementById(generationMode + '-mode').style.display = 'block';
        }
    });

    // Generate preview
    document.getElementById('generate-preview').addEventListener('click', function() {
        const type = document.getElementById('type').value;
        const generationMode = document.getElementById('generation_mode').value;
        
        if (!type || !generationMode) {
            alert('Please select barcode type and generation mode first.');
            return;
        }

        let code = '';
        
        if (generationMode === 'single') {
            const productSelect = document.getElementById('product_id');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            if (selectedOption.value) {
                code = selectedOption.text.match(/\(([^)]+)\)/)[1]; // Extract SKU from text
            }
        } else if (generationMode === 'custom') {
            code = document.getElementById('custom_code').value;
        }

        if (!code) {
            alert('Please provide a code to generate preview.');
            return;
        }

        // Generate preview (this would be an AJAX call in a real application)
        const previewDiv = document.getElementById('barcode-preview');
        previewDiv.innerHTML = `
            <div class="bg-light rounded p-4">
                <div class="barcode-placeholder" style="height: 60px; background: linear-gradient(90deg, #000 2px, transparent 2px), linear-gradient(90deg, #000 1px, transparent 1px); background-size: 10px 100%, 5px 100%; background-position: 0 0, 5px 0;"></div>
                <p class="mt-2 mb-0"><small><code>${code}</code></small></p>
                <small class="text-muted">${type} Preview</small>
            </div>
        `;
    });

    // Form validation
    document.getElementById('barcode-form').addEventListener('submit', function(e) {
        const type = document.getElementById('type').value;
        const generationMode = document.getElementById('generation_mode').value;
        
        if (!type || !generationMode) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return;
        }
        
        if (generationMode === 'single' && !document.getElementById('product_id').value) {
            e.preventDefault();
            alert('Please select a product.');
            return;
        }
        
        if (generationMode === 'custom' && !document.getElementById('custom_code').value) {
            e.preventDefault();
            alert('Please enter a custom code.');
            return;
        }
        
        if (generationMode === 'multiple') {
            const checkedProducts = document.querySelectorAll('input[name="products[]"]:checked');
            if (checkedProducts.length === 0) {
                e.preventDefault();
                alert('Please select at least one product.');
                return;
            }
        }
    });
</script>
@endpush 