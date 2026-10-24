<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /** Mengelola profil: form data diri dan form ganti kata sandi. */
    public function edit(Request $request): View
    {
        return view('pelanggan.profil.edit', ['pelanggan' => $request->user('pelanggan')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $pelanggan = $request->user('pelanggan');
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);

        $data = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:100'],
            'email'          => ['required', 'string', 'email', 'max:255', Rule::unique('pelanggans', 'email')->ignore($pelanggan->id_pelanggan, 'id_pelanggan')],
            'no_telp'        => ['required', 'string', 'regex:/^[0-9+\-\s]{9,20}$/'],
            'alamat'         => ['nullable', 'string', 'max:500'],
        ], $this->pesan(), $this->atribut());

        $pelanggan->update($data);

        return redirect()
            ->route('pelanggan.profil.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password_lama' => ['required', 'string', 'current_password:pelanggan'],
            'password'      => ['required', 'confirmed', Password::min(8)],
        ], $this->pesan(), $this->atribut());

        $request->user('pelanggan')->update(['password' => $data['password']]);

        return redirect()
            ->route('pelanggan.profil.edit')
            ->with('success', 'Kata sandi berhasil diganti.');
    }

    private function pesan(): array
    {
        return [
            'required'         => ':attribute wajib diisi.',
            'email'            => 'Format :attribute tidak valid.',
            'max'              => ':attribute maksimal :max karakter.',
            'unique'           => ':attribute sudah dipakai akun lain.',
            'regex'            => 'Nomor telepon tidak valid.',
            'confirmed'        => 'Konfirmasi kata sandi tidak cocok.',
            'current_password' => 'Kata sandi lama salah.',
            'password.min'     => 'Kata sandi minimal :min karakter.',
        ];
    }

    private function atribut(): array
    {
        return [
            'nama_pelanggan' => 'Nama',
            'email'          => 'Email',
            'no_telp'        => 'Nomor telepon',
            'alamat'         => 'Alamat',
            'password_lama'  => 'Kata sandi lama',
            'password'       => 'Kata sandi baru',
        ];
    }
}
