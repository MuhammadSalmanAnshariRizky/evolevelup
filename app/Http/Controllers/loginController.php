<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class loginController extends Controller
{
    public function showLoginForm()
    {
        return view('home.login');
    }

    /**
     * Memproses login pengguna.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan.')->onlyInput('email');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Kata sandi yang Anda masukkan salah.')->onlyInput('email');
        }

        // 🔑 PERBAIKAN: Login-kan user dan perbarui session ID
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect berdasarkan role
        if ($user->role === 'teacher') {
            return redirect()->route('dashboardGuru')->with('success', 'Selamat datang, Guru!');
        }

        if ($user->role === 'student') {
            return redirect()->route('dashboard.siswa')->with('success', 'Selamat datang, Siswa!');
        }

        return redirect('/')->with('success', 'Selamat datang di Evolevel!');
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah keluar.');
    }
}