<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /** Peran yang bisa masuk. Nilainya sama dengan nama guard di config/auth.php. */
    public const PERAN = ['pelanggan', 'admin'];

    private const MAKSIMAL_PERCOBAAN = 5;

    /** Form login bersama untuk pelanggan dan admin. */
    public function create(): View
    {
        return view('auth.login');
    }

    /** Proses login. Pelanggan dan admin dicek ke tabelnya masing-masing. */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);

        $data = $request->validate([
            'peran'    => ['required', Rule::in(self::PERAN)],
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], $this->pesan(), $this->atribut());

        $kunciPercobaan = Str::transliterate($data['peran'].'|'.$data['email'].'|'.$request->ip());
        $this->pastikanTidakTerlaluBanyakPercobaan($kunciPercobaan);

        $berhasil = Auth::guard($data['peran'])->attempt([
            'email'       => $data['email'],
            'password'    => $data['password'],
            'status_akun' => 'aktif',
        ], $request->boolean('ingat_saya'));

        if (! $berhasil) {
            RateLimiter::hit($kunciPercobaan);

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah, atau akun tidak aktif.',
            ]);
        }

        RateLimiter::clear($kunciPercobaan);
        $request->session()->regenerate();

        if ($data['peran'] === 'admin') {
            return redirect()->intended(route('home'));
        }

        $this->lupakanTujuanHalamanAdmin($request);

        return redirect()->intended(route('home'));
    }

    /** Logout dari semua peran, lalu kosongkan sesi. */
    public function destroy(Request $request): RedirectResponse
    {
        foreach (self::PERAN as $peran) {
            Auth::guard($peran)->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /** Pelanggan tidak boleh diarahkan ke halaman admin yang tadi dicoba dibuka sebelum login. */
    private function lupakanTujuanHalamanAdmin(Request $request): void
    {
        $tujuan = (string) parse_url((string) $request->session()->get('url.intended'), PHP_URL_PATH);

        if ($tujuan === '/admin' || Str::startsWith($tujuan, '/admin/')) {
            $request->session()->forget('url.intended');
        }
    }

    private function pastikanTidakTerlaluBanyakPercobaan(string $kunci): void
    {
        if (! RateLimiter::tooManyAttempts($kunci, self::MAKSIMAL_PERCOBAAN)) {
            return;
        }

        $detik = RateLimiter::availableIn($kunci);

        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$detik} detik.",
        ]);
    }

    private function pesan(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'email'    => 'Format :attribute tidak valid.',
            'in'       => 'Pilihan peran tidak valid.',
        ];
    }

    private function atribut(): array
    {
        return [
            'peran'    => 'Peran',
            'email'    => 'Email',
            'password' => 'Kata sandi',
        ];
    }
}
