<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /** Use case: Login (Admin) - tampilkan form. */
    public function formLogin(): View
    {
        return view('auth.login', [
            'peran'  => 'admin',
            'action' => route('admin.login.proses'),
        ]);
    }

    /** Use case: Login (Admin) - proses. */
    public function login(Request $request): RedirectResponse
    {
        $kredensial = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'required' => ':attribute wajib diisi.',
            'email'    => 'Format email tidak valid.',
        ]);

        $kredensial['email'] = strtolower($kredensial['email']);

        if (Auth::guard('admin')->attempt($kredensial + ['status_akun' => 'aktif'], $request->boolean('ingat'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'Email atau kata sandi salah, atau akun tidak aktif.'])
            ->onlyInput('email');
    }

    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'admin'         => Auth::guard('admin')->user(),
            'totalPesanan'  => Pesanan::count(),
            'pesananBaru'   => Pesanan::where('status', 'baru')->count(),
            'totalPelanggan' => Pelanggan::count(),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
