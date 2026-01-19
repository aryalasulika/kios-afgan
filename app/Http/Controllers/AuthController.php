<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginKasirForm()
    {
        return view('auth.kasir-login');
    }

    public function loginAdminForm()
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $ops = ['+', '*', '-'];
        $operator = $ops[array_rand($ops)];

        switch ($operator) {
            case '+':
                $result = $num1 + $num2;
                break;
            case '*':
                $result = $num1 * $num2;
                break;
            case '-':
                $result = $num1 - $num2;
                break;
        }

        session(['captcha_answer' => $result]);

        return view('auth.admin-login', compact('num1', 'num2', 'operator'));
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
            'captcha' => ['required', 'numeric'],
        ]);

        if ((int) $request->captcha !== (int) session('captcha_answer')) {
            return back()->withErrors([
                'captcha' => 'Verifikasi gagal. Jawaban matematika salah.',
            ])->withInput();
        }

        $throttleKey = Str::lower($request->input('username')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'username' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.',
            ]);
        }

        $user = \App\Models\User::where('username', $credentials['username'])
            ->where('role', 'admin')
            ->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            RateLimiter::clear($throttleKey);
            // Use 'admin' guard
            \Illuminate\Support\Facades\Auth::guard('admin')->login($user);
            $request->session()->regenerate();
            return redirect('/admin');
        }

        RateLimiter::hit($throttleKey);

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
