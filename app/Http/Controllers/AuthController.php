<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            $role = auth()->user()->role;
            return match ($role) {
                'admin', 'staff' => redirect()->route('book.index'),
                'student', 'faculty' => redirect()->route('landing'),
                default => redirect()->route('login')->with('error', 'Unauthorized role.'),
            };
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            return match ($user->role) {
                'admin', 'staff' => redirect()->intended(route('book.index')),
                'student', 'faculty' => redirect()->intended('landing'),
                default => redirect()->route('login')->with('error', 'Unauthorized role.'),
            };
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
