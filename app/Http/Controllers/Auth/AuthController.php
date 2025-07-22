<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Setting;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Check if setup is completed
        $setupCompleted = Setting::where('key', 'setup_completed')
            ->where('value', true)
            ->exists();

        if (!$setupCompleted) {
            return redirect()->route('setup.index');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact the administrator.');
            }

            // Update last login time
            $user->last_login_at = now();
            $user->save();

            // Log the login activity
            activity()
                ->causedBy($user)
                ->log('User logged in');

            return redirect()->intended(route('dashboard'));
        }

        return redirect()->route('login')->with('error', 'Invalid credentials. Please try again.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log the logout activity
        if ($user) {
            activity()
                ->causedBy($user)
                ->log('User logged out');
        }

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
} 