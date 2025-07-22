<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = User::with(['roles', 'store']);
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Role filter
        if ($request->filled('role')) {
            $query->role($request->get('role'));
        }
        
        // Status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }
        
        $users = $query->orderBy('name')->paginate(20);
        $roles = Role::all();
        
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $stores = Store::all();
        
        return view('users.create', compact('roles', 'stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'store_id' => 'nullable|exists:stores,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'store_id' => $request->store_id,
            'is_active' => $request->boolean('is_active', true)
        ]);

        $user->assignRole($request->role);

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_created',
            'description' => "Created user: {$user->name} ({$user->email})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'store', 'sales', 'activityLogs']);
        
        // Get user statistics
        $stats = [
            'total_sales' => $user->sales()->count(),
            'total_sales_amount' => $user->sales()->sum('total'),
            'avg_sale_amount' => $user->sales()->avg('total') ?: 0,
            'last_login' => $user->activityLogs()
                ->where('action', 'login')
                ->latest()
                ->first()?->created_at
        ];
        
        return view('users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        // Prevent editing super admin
        if ($user->hasRole('super-admin')) {
            return redirect()->back()->with('error', 'Cannot edit super admin user.');
        }
        
        $roles = Role::all();
        $stores = Store::all();
        $user->load('roles');
        
        return view('users.edit', compact('user', 'roles', 'stores'));
    }

    public function update(Request $request, User $user)
    {
        // Prevent editing super admin
        if ($user->hasRole('super-admin')) {
            return redirect()->back()->with('error', 'Cannot edit super admin user.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'store_id' => 'nullable|exists:stores,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'store_id' => $request->store_id,
            'is_active' => $request->boolean('is_active', true)
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update role
        $user->syncRoles([$request->role]);

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_updated',
            'description' => "Updated user: {$user->name} ({$user->email})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting super admin or current user
        if ($user->hasRole('super-admin')) {
            return redirect()->back()->with('error', 'Cannot delete super admin user.');
        }
        
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Cannot delete your own account.');
        }
        
        // Check if user has sales
        if ($user->sales()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete user with existing sales records.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete();

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_deleted',
            'description' => "Deleted user: {$userName} ({$userEmail})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        // Prevent deactivating super admin or current user
        if ($user->hasRole('super-admin')) {
            return redirect()->back()->with('error', 'Cannot deactivate super admin user.');
        }
        
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_status_changed',
            'description' => "User {$status}: {$user->name} ({$user->email})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()
            ->with('success', "User {$status} successfully.");
    }

    public function resetPassword(User $user)
    {
        // Generate random password
        $newPassword = 'password123'; // In production, use a random generator
        
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'password_reset',
            'description' => "Reset password for user: {$user->name} ({$user->email})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()
            ->with('success', "Password reset successfully. New password: {$newPassword}");
    }

    public function activityLogs(User $user)
    {
        $logs = $user->activityLogs()
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        return view('users.activity-logs', compact('user', 'logs'));
    }
} 