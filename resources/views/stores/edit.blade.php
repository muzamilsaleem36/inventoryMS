@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Store: {{ $store->name }}
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('stores.show', $store->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('stores.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('stores.update', $store->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Store Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">
                                                        Store Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $store->name) }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="code" class="form-label">
                                                        Store Code <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $store->code) }}" required>
                                                    @error('code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Enter store description">{{ old('description', $store->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">Phone Number</label>
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $store->phone) }}" placeholder="Enter phone number">
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email Address</label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $store->email) }}" placeholder="Enter email address">
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" placeholder="Enter store address">{{ old('address', $store->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="city" class="form-label">City</label>
                                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $store->city) }}" placeholder="Enter city">
                                                    @error('city')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="postal_code" class="form-label">Postal Code</label>
                                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code', $store->postal_code) }}" placeholder="Enter postal code">
                                                    @error('postal_code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="country" class="form-label">Country</label>
                                                    <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', $store->country) }}" placeholder="Enter country">
                                                    @error('country')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">
                                                        Status <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                        <option value="">Select status</option>
                                                        <option value="active" {{ old('status', $store->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ old('status', $store->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Store Logo</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($store->logo)
                                            <div class="current-logo mb-3 text-center">
                                                <label class="form-label">Current Logo</label>
                                                <div>
                                                    <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->name }}" class="img-fluid rounded" style="max-height: 200px;">
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="mb-3">
                                            <label for="logo" class="form-label">
                                                {{ $store->logo ? 'Change Logo' : 'Upload Logo' }}
                                            </label>
                                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                                            @error('logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Supported formats: JPG, PNG, GIF. Max size: 2MB.
                                            </small>
                                        </div>
                                        
                                        <div class="image-preview mt-3 text-center" style="display: none;">
                                            <label class="form-label">Preview</label>
                                            <div>
                                                <img id="imagePreview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                            </div>
                                        </div>

                                        @if($store->logo)
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="remove_logo" name="remove_logo" value="1">
                                                    <label class="form-check-label" for="remove_logo">
                                                        Remove current logo
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Business Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="tax_number" class="form-label">Tax Number</label>
                                            <input type="text" class="form-control @error('tax_number') is-invalid @enderror" id="tax_number" name="tax_number" value="{{ old('tax_number', $store->tax_number) }}" placeholder="Enter tax number">
                                            @error('tax_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="currency" class="form-label">Currency</label>
                                            <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency">
                                                <option value="">Select currency</option>
                                                <option value="USD" {{ old('currency', $store->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                                <option value="EUR" {{ old('currency', $store->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                                <option value="GBP" {{ old('currency', $store->currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                                <option value="CAD" {{ old('currency', $store->currency) == 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                                <option value="AUD" {{ old('currency', $store->currency) == 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                                            </select>
                                            @error('currency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="timezone" class="form-label">Timezone</label>
                                            <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone">
                                                <option value="">Select timezone</option>
                                                <option value="UTC" {{ old('timezone', $store->timezone) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                                <option value="America/New_York" {{ old('timezone', $store->timezone) == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                                <option value="America/Chicago" {{ old('timezone', $store->timezone) == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                                <option value="America/Denver" {{ old('timezone', $store->timezone) == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                                <option value="America/Los_Angeles" {{ old('timezone', $store->timezone) == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                                <option value="Europe/London" {{ old('timezone', $store->timezone) == 'Europe/London' ? 'selected' : '' }}>London Time</option>
                                                <option value="Europe/Paris" {{ old('timezone', $store->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Central European Time</option>
                                                <option value="Asia/Tokyo" {{ old('timezone', $store->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Japan Time</option>
                                                <option value="Asia/Shanghai" {{ old('timezone', $store->timezone) == 'Asia/Shanghai' ? 'selected' : '' }}>China Time</option>
                                                <option value="Australia/Sydney" {{ old('timezone', $store->timezone) == 'Australia/Sydney' ? 'selected' : '' }}>Sydney Time</option>
                                            </select>
                                            @error('timezone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Store Manager</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="manager_id" class="form-label">Assign Manager</label>
                                            <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id">
                                                <option value="">Select manager (optional)</option>
                                                @foreach($managers as $manager)
                                                    <option value="{{ $manager->id }}" {{ old('manager_id', $store->manager_id) == $manager->id ? 'selected' : '' }}>
                                                        {{ $manager->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('manager_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Store Statistics</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Staff Members:</strong> {{ $store->users->count() }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Created:</strong> {{ $store->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Updated:</strong> {{ $store->updated_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('stores.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Store
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
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.querySelector('.image-preview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const code = document.getElementById('code').value.trim();
        const status = document.getElementById('status').value;
        
        if (!name) {
            e.preventDefault();
            alert('Please enter a store name.');
            document.getElementById('name').focus();
            return;
        }
        
        if (!code) {
            e.preventDefault();
            alert('Please enter a store code.');
            document.getElementById('code').focus();
            return;
        }
        
        if (!status) {
            e.preventDefault();
            alert('Please select a status.');
            document.getElementById('status').focus();
            return;
        }
    });
</script>
@endpush 