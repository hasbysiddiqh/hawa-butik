<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /** Form registrasi akun pelanggan. Admin tidak mendaftar sendiri. */
    public function create(): View
    {
        return view('auth.register');
    }

    /** Simpan akun pelanggan baru, lalu langsung masuk. */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);

        $data = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:100'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:pelanggans,email'],
            'no_telp'        => ['required', 'string', 'regex:/^[0-9+\-\s]{9,20}$/'],
            'alamat'         => ['nullable', 'string', 'max:500'],
            'password'       => ['required', 'confirmed', Password::min(8)],
        ], $this->pesan(), $this->atribut());

        $pelanggan = Pelanggan::create([
            ...$data,
            'tanggal_daftar' => now(),
            'status_akun'    => 'aktif',
        ]);

        Auth::guard('pelanggan')->login($pelanggan);
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'Registrasi berhasil. Selamat datang di Hawa Butik!');
    }

    private function pesan(): array
    {
        return [
            'required'     => ':attribute wajib diisi.',
            'email'        => 'Format :attribute tidak valid.',
            'max'          => ':attribute maksimal :max karakter.',
            'unique'       => ':attribute sudah terdaftar.',
            'regex'        => 'Nomor telepon tidak valid.',
            'confirmed'    => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal :min karakter.',
        ];
    }

    private function atribut(): array
    {
        return [
            'nama_pelanggan' => 'Nama',
            'email'          => 'Email',
            'no_telp'        => 'Nomor telepon',
            'alamat'         => 'Alamat',
            'password'       => 'Kata sandi',
        ];
    }
}
