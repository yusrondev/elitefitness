<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'email' => ['required', 'string', 'email'],
    //         'password' => ['required', 'string'],
    //     ]);

    //     if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
    //         $request->session()->regenerate();

    //         // Cek role pengguna dan arahkan ke dashboard yang sesuai
    //         if (auth()->user()->isAdmin()) {
    //             return redirect()->route('admin.dashboard');
    //         }

    //         if (auth()->user()->isTrainer()) {
    //             return redirect()->route('trainer.dashboard');
    //         }

    //         // Default: Redirect ke dashboard user
    //         return redirect()->route('admin.dashboard');
    //     }

    //     return back()->withInput()->with('login_error', 'Email atau password salah.');
    // }
    
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
    
        $credentials = $request->only('email', 'password');
    
        \Log::info('Credentials received', $credentials);
    
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
    
            // redirect sesuai role...
            return redirect()->intended('dashboard');
        }
    
        \Log::warning('Login failed', $credentials);
    
        return back()->withInput()->with('login_error', 'Email atau password salah.');
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
