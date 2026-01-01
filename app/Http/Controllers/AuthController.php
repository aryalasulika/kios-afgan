<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loginKasirForm()
    {
        return view('auth.kasir-login');
    }

    public function loginAdminForm()
    {
        return view('auth.admin-login');
    }

    public function loginKasir(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'pin' => ['required'],
        ]);

        $user = \App\Models\User::where('username', $credentials['username'])
            ->where('role', 'kasir')
            ->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($credentials['pin'], $user->pin)) {
            // Use 'kasir' guard
            \Illuminate\Support\Facades\Auth::guard('kasir')->login($user);
            $request->session()->regenerate();
            return redirect('/kasir');
        }

        return back()->withErrors([
            'username' => 'PIN salah atau user tidak ditemukan.',
        ]);
    }

    public function loginAdmin(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $user = \App\Models\User::where('username', $credentials['username'])
            ->where('role', 'admin')
            ->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            // Use 'admin' guard
            \Illuminate\Support\Facades\Auth::guard('admin')->login($user);
            $request->session()->regenerate();
            return redirect('/admin');
        }

        return back()->withErrors([
            'username' => 'Password salah atau user tidak ditemukan.',
        ]);
    }

    public function logout(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::guard('admin')->check()) {
            \Illuminate\Support\Facades\Auth::guard('admin')->logout();
            return redirect()->route('admin.login');
        }

        if (\Illuminate\Support\Facades\Auth::guard('kasir')->check()) {
            \Illuminate\Support\Facades\Auth::guard('kasir')->logout();
            return redirect()->route('kasir.login');
        }

        // Fallback
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
