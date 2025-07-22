@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-print me-2"></i>
                        Bulk Print Barcodes
                    </h3>
                    <a href="{{ route('barcodes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('barcodes.bulk-print.process') }}" method="POST" id="bulk-print-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Select Barcodes to Print</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Selection Method -->
                                        <div class="mb-3">
                                            <label class="form-label">Selection Method</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="selection_method" id="method_all" value="all" checked>
                                                <label class="form-check-label" for="method_all">
                                                    All Active Barcodes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="selection_method" id="method_category" value="category">
                                                <label class="form-check-label" for="method_category">
                                                    By Product Category
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="selection_method" id="method_custom" value="custom">
                                                <label class="form-check-label" for="method_custom">
                                                    Custom Selection
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Category Selection -->
                                        <div id="category-selection" class="selection-method" style="display: none;">
                                            <div class="mb-3">
                                                <label for="categories" class="form-label">Select Categories</label>
                                                <select class="form-select" id="categories" name="categories[]" multiple>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">Hold Ctrl to select multiple categories</small>
                                            </div>
                                        </div>

                                        <!-- Custom Selection -->
                                        <div id="custom-selection" class="selection-method" style="display: none;">
                                            <div class="mb-3">
                                                <label class="form-label">Select Barcodes</label>
                                                <div class="table-responsive" style="max-height: 400px;">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    <input type="checkbox" id="select-all-custom" class="form-check-input">
                                                                </th>
                                                                <th>Product</th>
                                                                <th>Code</th>
                                                                <th>Type</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($barcodes as $barcode)
                                                                <tr>
                                                                    <td>
                                                                        <input type="checkbox" name="selected_barcodes[]" value="{{ $barcode->id }}" class="form-check-input custom-barcode-checkbox">
                                                                    </td>
                                                                    <td>
                                                                        @if($barcode->product)
                                                                            {{ $barcode->product->name }}
                                                                        @else
                                                                            <span class="text-muted">No product</span>
                                                                        @endif
                                                                    </td>
                                                                    <td><code>{{ $barcode->code }}</code></td>
                                                                    <td><span class="badge bg-secondary">{{ $barcode->type }}</span></td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Print Quantity -->
                                        <div class="mb-3">
                                            <label for="copies_per_barcode" class="form-label">Copies per Barcode</label>
                                            <input type="number" class="form-control @error('copies_per_barcode') is-invalid @enderror" id="copies_per_barcode" name="copies_per_barcode" value="{{ old('copies_per_barcode', 1) }}" min="1" max="100">
                                            @error('copies_per_barcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Print Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="paper_size" class="form-label">Paper Size</label>
                                                    <select class="form-select @error('paper_size') is-invalid @enderror" id="paper_size" name="paper_size">
                                                        <option value="A4" {{ old('paper_size') == 'A4' ? 'selected' : '' }}>A4 (210 x 297 mm)</option>
                                                        <option value="Letter" {{ old('paper_size') == 'Letter' ? 'selected' : '' }}>Letter (8.5 x 11 in)</option>
                                                        <option value="Label" {{ old('paper_size') == 'Label' ? 'selected' : '' }}>Label Sheet</option>
                                                    </select>
                                                    @error('paper_size')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="orientation" class="form-label">Orientation</label>
                                                    <select class="form-select @error('orientation') is-invalid @enderror" id="orientation" name="orientation">
                                                        <option value="portrait" {{ old('orientation') == 'portrait' ? 'selected' : '' }}>Portrait</option>
                                                        <option value="landscape" {{ old('orientation') == 'landscape' ? 'selected' : '' }}>Landscape</option>
                                                    </select>
                                                    @error('orientation')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="labels_per_row" class="form-label">Labels per Row</label>
                                                    <input type="number" class="form-control @error('labels_per_row') is-invalid @enderror" id="labels_per_row" name="labels_per_row" value="{{ old('labels_per_row', 3) }}" min="1" max="10">
                                                    @error('labels_per_row')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="labels_per_column" class="form-label">Labels per Column</label>
                                                    <input type="number" class="form-control @error('labels_per_column') is-invalid @enderror" id="labels_per_column" name="labels_per_column" value="{{ old('labels_per_column', 7) }}" min="1" max="20">
                                                    @error('labels_per_column')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="label_width" class="form-label">Label Width (mm)</label>
                                                    <input type="number" class="form-control @error('label_width') is-invalid @enderror" id="label_width" name="label_width" value="{{ old('label_width', 70) }}" min="20" max="200">
                                                    @error('label_width')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="label_height" class="form-label">Label Height (mm)</label>
                                                    <input type="number" class="form-control @error('label_height') is-invalid @enderror" id="label_height" name="label_height" value="{{ old('label_height', 40) }}" min="10" max="100">
                                                    @error('label_height')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
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
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="include_price" name="include_price" value="1" {{ old('include_price') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="include_price">
                                                            Include price
                                                        </label>
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
                                        <h5 class="card-title mb-0">Print Preview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="print-preview" class="text-center">
                                            <div class="preview-container" style="border: 1px solid #ddd; padding: 20px; background: white;">
                                                <div class="preview-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                                                    <div class="preview-label" style="border: 1px dashed #ccc; padding: 10px; text-align: center; min-height: 60px;">
                                                        <div class="barcode-placeholder" style="height: 20px; background: linear-gradient(90deg, #000 1px, transparent 1px); background-size: 3px 100%;"></div>
                                                        <small class="text-muted">Sample</small>
                                                    </div>
                                                    <div class="preview-label" style="border: 1px dashed #ccc; padding: 10px; text-align: center; min-height: 60px;">
                                                        <div class="barcode-placeholder" style="height: 20px; background: linear-gradient(90deg, #000 1px, transparent 1px); background-size: 3px 100%;"></div>
                                                        <small class="text-muted">Sample</small>
                                                    </div>
                                                    <div class="preview-label" style="border: 1px dashed #ccc; padding: 10px; text-align: center; min-height: 60px;">
                                                        <div class="barcode-placeholder" style="height: 20px; background: linear-gradient(90deg, #000 1px, transparent 1px); background-size: 3px 100%;"></div>
                                                        <small class="text-muted">Sample</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-center">
                                            <button type="button" id="update-preview" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> Update Preview
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Print Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Total Barcodes:</strong> <span id="total-barcodes">-</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Total Copies:</strong> <span id="total-copies">-</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Pages Required:</strong> <span id="pages-required">-</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Paper Size:</strong> <span id="paper-size-display">A4</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Orientation:</strong> <span id="orientation-display">Portrait</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Print Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="submit" name="action" value="preview" class="btn btn-info">
                                                <i class="fas fa-eye"></i> Generate Preview
                                            </button>
                                            <button type="submit" name="action" value="print" class="btn btn-success">
                                                <i class="fas fa-print"></i> Print Now
                                            </button>
                                            <button type="submit" name="action" value="download" class="btn btn-primary">
                                                <i class="fas fa-download"></i> Download PDF
                                            </button>
                                        </div>
                                    </div>
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
    // Selection method change handler
    document.querySelectorAll('input[name="selection_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const methods = document.querySelectorAll('.selection-method');
            methods.forEach(method => method.style.display = 'none');
            
            if (this.value === 'category') {
                document.getElementById('category-selection').style.display = 'block';
            } else if (this.value === 'custom') {
                document.getElementById('custom-selection').style.display = 'block';
            }
            
            updateSummary();
        });
    });

    // Select all custom checkboxes
    document.getElementById('select-all-custom').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.custom-barcode-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSummary();
    });

    // Individual checkbox change
    document.querySelectorAll('.custom-barcode-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSummary);
    });

    // Update preview on settings change
    document.getElementById('labels_per_row').addEventListener('input', updatePreview);
    document.getElementById('paper_size').addEventListener('change', updatePreview);
    document.getElementById('orientation').addEventListener('change', updatePreview);

    // Update preview manually
    document.getElementById('update-preview').addEventListener('click', updatePreview);

    // Update print summary
    function updateSummary() {
        const selectionMethod = document.querySelector('input[name="selection_method"]:checked').value;
        const copiesPerBarcode = parseInt(document.getElementById('copies_per_barcode').value) || 1;
        const labelsPerRow = parseInt(document.getElementById('labels_per_row').value) || 3;
        const labelsPerColumn = parseInt(document.getElementById('labels_per_column').value) || 7;
        const labelsPerPage = labelsPerRow * labelsPerColumn;
        
        let totalBarcodes = 0;
        
        if (selectionMethod === 'all') {
            totalBarcodes = {{ $barcodes->count() }};
        } else if (selectionMethod === 'custom') {
            totalBarcodes = document.querySelectorAll('.custom-barcode-checkbox:checked').length;
        } else if (selectionMethod === 'category') {
            // This would need to be calculated based on selected categories
            totalBarcodes = 0; // Placeholder
        }
        
        const totalCopies = totalBarcodes * copiesPerBarcode;
        const pagesRequired = Math.ceil(totalCopies / labelsPerPage);
        
        document.getElementById('total-barcodes').textContent = totalBarcodes;
        document.getElementById('total-copies').textContent = totalCopies;
        document.getElementById('pages-required').textContent = pagesRequired;
        document.getElementById('paper-size-display').textContent = document.getElementById('paper_size').value;
        document.getElementById('orientation-display').textContent = document.getElementById('orientation').value;
    }

    // Update preview layout
    function updatePreview() {
        const labelsPerRow = parseInt(document.getElementById('labels_per_row').value) || 3;
        const previewGrid = document.querySelector('.preview-grid');
        
        previewGrid.style.gridTemplateColumns = `repeat(${labelsPerRow}, 1fr)`;
        
        // Clear existing labels
        previewGrid.innerHTML = '';
        
        // Add sample labels
        for (let i = 0; i < labelsPerRow * 2; i++) {
            const label = document.createElement('div');
            label.className = 'preview-label';
            label.style.cssText = 'border: 1px dashed #ccc; padding: 10px; text-align: center; min-height: 60px;';
            label.innerHTML = `
                <div class="barcode-placeholder" style="height: 20px; background: linear-gradient(90deg, #000 1px, transparent 1px); background-size: 3px 100%;"></div>
                <small class="text-muted">Sample</small>
            `;
            previewGrid.appendChild(label);
        }
    }

    // Form validation
    document.getElementById('bulk-print-form').addEventListener('submit', function(e) {
        const selectionMethod = document.querySelector('input[name="selection_method"]:checked').value;
        
        if (selectionMethod === 'custom') {
            const selectedCount = document.querySelectorAll('.custom-barcode-checkbox:checked').length;
            if (selectedCount === 0) {
                e.preventDefault();
                alert('Please select at least one barcode to print.');
                return;
            }
        }
        
        if (selectionMethod === 'category') {
            const selectedCategories = document.getElementById('categories').selectedOptions.length;
            if (selectedCategories === 0) {
                e.preventDefault();
                alert('Please select at least one category.');
                return;
            }
        }
    });

    // Initialize
    updateSummary();
    updatePreview();
</script>
@endpush 