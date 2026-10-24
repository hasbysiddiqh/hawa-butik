<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PelangganAuthController extends Controller
{
    /** Use case: Login (Pelanggan) - tampilkan form. */
    public function formLogin(): View
    {
        return view('auth.login', [
            'peran'  => 'pelanggan',
            'action' => route('login.proses'),
        ]);
    }

    /** Use case: Login (Pelanggan) - proses. */
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

        // Hanya akun berstatus "aktif" yang boleh masuk.
        if (Auth::guard('pelanggan')->attempt($kredensial + ['status_akun' => 'aktif'], $request->boolean('ingat'))) {
            $request->session()->regenerate();

            return redirect()->intended(url('/'));
        }

        return back()
            ->withErrors(['email' => 'Email atau kata sandi salah, atau akun tidak aktif.'])
            ->onlyInput('email');
    }

    /** Use case: Registrasi Akun - tampilkan form. */
    public function formDaftar(): View
    {
        return view('auth.daftar');
    }

    /** Use case: Registrasi Akun - simpan pelanggan baru. */
    public function daftar(Request $request): RedirectResponse
    {
        $request->merge(['email' => strtolower((string) $request->input('email'))]);

        $data = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'max:150', 'unique:pelanggans,email'],
            'no_telp'        => ['required', 'string', 'regex:/^[0-9+\-\s]{9,20}$/'],
            'alamat'         => ['required', 'string', 'max:500'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'required'  => ':attribute wajib diisi.',
            'email'     => 'Format email tidak valid.',
            'unique'    => 'Email sudah terdaftar. Silakan masuk.',
            'regex'     => 'Nomor telepon tidak valid.',
            'min'       => ':attribute minimal :min karakter.',
            'confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ], [
            'nama_pelanggan' => 'Nama lengkap',
            'no_telp'        => 'Nomor telepon',
            'alamat'         => 'Alamat',
            'password'       => 'Kata sandi',
        ]);

        Pelanggan::create($data + [
            'tanggal_daftar' => now()->toDateString(),
            'status_akun'    => 'aktif',
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Akun berhasil dibuat. Silakan masuk.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('pelanggan')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
