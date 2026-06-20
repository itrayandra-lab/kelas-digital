<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required'], // Bisa username atau email
            'password' => ['required'],
        ]);

        // Cek apakah input adalah email atau username
        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials[$field] = $credentials['login'];
        unset($credentials['login']);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Update last login
            $user = User::find(Auth::id());
            if ($user) {
                $user->last_login = now();
                $user->save();
            }

            // Redirect based on user role
            if ($user && ($user->isSuperAdmin() || $user->isAdmin() || $user->isInstructor() || $user->isContentManager())) {
                return redirect()->intended('/admin');
            }

            // Student role goes to dashboard
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:20', 'unique:users', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'indisposable'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign student role
        $user->assignRole('student');

        // Kirim welcome email (cek apakah email aktif)
        try {
            $user->notify(new WelcomeNotification($user));
        } catch (\Throwable $e) {
            // Jika gagal, log saja, akun tetap terdaftar
            logger('Gagal kirim email ke '.$user->email.': '.$e->getMessage());
        }

        Auth::login($user);

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
