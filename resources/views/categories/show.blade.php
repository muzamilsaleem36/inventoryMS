@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-tag me-2"></i>
                        Category Details
                    </h3>
                    <div class="btn-group">
                        @can('category-edit')
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                        @can('category-delete')
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endcan
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Category Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($category->image)
                                        <div class="text-center mb-3">
                                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-fluid rounded" style="max-height: 200px;">
                                        </div>
                                    @else
                                        <div class="text-center mb-3">
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                                <i class="fas fa-tag fa-3x text-white"></i>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $category->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Code:</strong></td>
                                            <td>{{ $category->code ?? 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($category->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Parent Category:</strong></td>
                                            <td>
                                                @if($category->parent)
                                                    <a href="{{ route('categories.show', $category->parent->id) }}" class="text-decoration-none">
                                                        {{ $category->parent->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sort Order:</strong></td>
                                            <td>{{ $category->sort_order ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $category->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Updated:</strong></td>
                                            <td>{{ $category->updated_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($category->description)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-align-left me-2"></i>
                                            Description
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $category->description }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($category->meta_title || $category->meta_description || $category->slug)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-search me-2"></i>
                                            SEO Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless table-sm">
                                            @if($category->meta_title)
                                                <tr>
                                                    <td><strong>Meta Title:</strong></td>
                                                    <td>{{ $category->meta_title }}</td>
                                                </tr>
                                            @endif
                                            @if($category->meta_description)
                                                <tr>
                                                    <td><strong>Meta Description:</strong></td>
                                                    <td>{{ $category->meta_description }}</td>
                                                </tr>
                                            @endif
                                            @if($category->slug)
                                                <tr>
                                                    <td><strong>Slug:</strong></td>
                                                    <td><code>{{ $category->slug }}</code></td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        Category Statistics
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-box fa-2x mb-2"></i>
                                                    <h4>{{ $category->products->count() }}</h4>
                                                    <p class="mb-0">Total Products</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                                    <h4>{{ $category->products->where('status', 'active')->count() }}</h4>
                                                    <p class="mb-0">Active Products</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-warehouse fa-2x mb-2"></i>
                                                    <h4>{{ $category->products->sum('stock_quantity') }}</h4>
                                                    <p class="mb-0">Total Stock</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                                    <h4>${{ number_format($category->products->sum('price'), 2) }}</h4>
                                                    <p class="mb-0">Total Value</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($category->children && $category->children->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-sitemap me-2"></i>
                                            Sub-Categories
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($category->children as $child)
                                                <div class="col-md-6 mb-3">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                @if($child->image)
                                                                    <img src="{{ asset('storage/' . $child->image) }}" alt="{{ $child->name }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                                        <i class="fas fa-tag text-white"></i>
                                                                    </div>
                                                                @endif
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1">
                                                                        <a href="{{ route('categories.show', $child->id) }}" class="text-decoration-none">
                                                                            {{ $child->name }}
                                                                        </a>
                                                                    </h6>
                                                                    <small class="text-muted">{{ $child->products->count() }} products</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="card mt-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-box me-2"></i>
                                        Products in this Category
                                    </h5>
                                    @can('product-create')
                                        <a href="{{ route('products.create') }}?category_id={{ $category->id }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Add Product
                                        </a>
                                    @endcan
                                </div>
                                <div class="card-body">
                                    @if($category->products->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>SKU</th>
                                                        <th>Price</th>
                                                        <th>Stock</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($category->products->take(10) as $product)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    @if($product->image)
                                                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                                    @else
                                                                        <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                            <i class="fas fa-box text-white"></i>
                                                                        </div>
                                                                    @endif
                                                                    <div>
                                                                        <strong>{{ $product->name }}</strong>
                                                                        @if($product->description)
                                                                            <br><small class="text-muted">{{ Str::limit($product->description, 30) }}</small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>{{ $product->sku }}</td>
                                                            <td>${{ number_format($product->price, 2) }}</td>
                                                            <td>
                                                                @if($product->stock_quantity > 0)
                                                                    <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                                                                @else
                                                                    <span class="badge bg-danger">Out of Stock</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($product->status == 'active')
                                                                    <span class="badge bg-success">Active</span>
                                                                @else
                                                                    <span class="badge bg-danger">Inactive</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @can('product-view')
                                                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('product-edit')
                                                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($category->products->count() > 10)
                                            <div class="text-center mt-3">
                                                <a href="{{ route('products.index') }}?category_id={{ $category->id }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-list"></i> View All Products
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No products found in this category.</p>
                                            @can('product-create')
                                                <a href="{{ route('products.create') }}?category_id={{ $category->id }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add First Product
                                                </a>
                                            @endcan
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 