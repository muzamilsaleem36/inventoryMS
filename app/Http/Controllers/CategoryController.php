<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_categories');
    }

    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Active status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }
        
        $categories = $query->orderBy('name')->paginate(20);
        
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:categories,code',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('categories', $imageName, 'public');
            $data['image'] = $imageName;
        }
        
        $category = Category::create($data);
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'category_created',
            "Created category: {$category->name}",
            $category
        );
        
        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $category->load(['products' => function($query) {
            $query->withCount('saleItems');
        }]);
        
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:categories,code,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $oldData = $category->toArray();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete('categories/' . $category->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('categories', $imageName, 'public');
            $data['image'] = $imageName;
        }
        
        $category->update($data);
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'category_updated',
            "Updated category: {$category->name}",
            $category,
            $oldData,
            $category->toArray()
        );
        
        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Cannot delete category with existing products.');
        }
        
        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete('categories/' . $category->image);
        }
        
        $categoryName = $category->name;
        $category->delete();
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'category_deleted',
            "Deleted category: {$categoryName}"
        );
        
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
} 