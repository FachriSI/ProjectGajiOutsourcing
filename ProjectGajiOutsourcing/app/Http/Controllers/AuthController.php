<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        // Validasi input dengan pesan error Indonesia
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email dan password harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Email dan password harus diisi',
        ]);

        try {
            // Coba autentikasi
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return redirect()->intended('dashboard');
            }

            // Jika kredensial salah (email tidak terdaftar ATAU password salah)
            // Gunakan pesan yang sama untuk keduanya demi keamanan
            return back()->withErrors([
                'login' => 'Email atau password yang Anda masukkan salah. Silakan periksa kembali.',
            ])->onlyInput('email');

        } catch (\Exception $e) {
            // Tangani server error
            \Log::error('Login error: ' . $e->getMessage());

            return back()->withErrors([
                'login' => 'Maaf, sistem sedang mengalami gangguan. Silakan coba beberapa saat lagi.',
            ])->onlyInput('email');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
