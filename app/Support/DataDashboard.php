<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Pembantu data untuk dashboard. SEMUA tebakan nama kolom ada di file ini:
 * kalau nama kolom di database berbeda, cukup tambahkan namanya ke daftar di bawah.
 */
class DataDashboard
{
    public static function pick($model, array $names, $default = null)
    {
        foreach ($names as $n) {
            $v = data_get($model, $n);
            if ($v !== null && $v !== '') {
                return $v;
            }
        }
        return $default;
    }

    /**
     * Tanggal dalam Bahasa Indonesia, mis. "Sabtu, 08 November 2026".
     * Ditulis manual supaya tidak bergantung pada locale aplikasi.
     */
    public static function tanggalIndonesia(?Carbon $tanggal = null): string
    {
        $tanggal ??= now();
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $hari[$tanggal->dayOfWeek].', '.$tanggal->format('d').' '.$bulan[$tanggal->month].' '.$tanggal->year;
    }

    /**
     * Status pesanan -> [label, kelas CSS, kunci ringkasan].
     * Nilai status dari database dipetakan persis (bukan ditebak dari kata), supaya
     * "Belum dikembalikan" tidak terbaca sebagai "Selesai".
     */
    public static function status($raw): array
    {
        $peta = [
            'baru'               => ['Baru', 'st-baru', 'baru'],
            'diproses'           => ['Dikerjakan', 'st-proses', 'proses'],
            'siap_diambil'       => ['Siap diambil', 'st-proses', 'proses'],
            'selesai'            => ['Selesai', 'st-selesai', 'selesai'],
            'dibatalkan'         => ['Dibatalkan', 'st-batal', 'batal'],
            'dipinjam'           => ['Dipinjam', 'st-proses', 'proses'],
            'belum_dikembalikan' => ['Belum kembali', 'st-telat', 'proses'],
            'dikembalikan'       => ['Dikembalikan', 'st-selesai', 'selesai'],
        ];

        $kunci = Str::lower((string) $raw);
        if (isset($peta[$kunci])) {
            return $peta[$kunci];
        }

        // Cadangan untuk nilai lama yang tidak ada di peta.
        if (Str::contains($kunci, 'batal')) return ['Dibatalkan', 'st-batal', 'batal'];
        if (Str::contains($kunci, ['selesai', 'lunas'])) return ['Selesai', 'st-selesai', 'selesai'];
        if (Str::contains($kunci, ['kerja', 'proses', 'jahit'])) return ['Dikerjakan', 'st-proses', 'proses'];
        return ['Baru', 'st-baru', 'baru'];
    }

    public static function foto($v): ?string
    {
        if (!$v) return null;
        return Str::startsWith($v, ['http', '/']) ? $v : asset('storage/' . $v);
    }

    public static function rupiah($v): string
    {
        return 'Rp ' . number_format((float) $v, 0, ',', '.');
    }

    /** Satu pesanan -> array sederhana untuk tampilan. */
    public static function baris($p): array
    {
        [$stLabel, $stKelas, $stKey] = self::status(self::pick($p, ['status_pesanan', 'status']));
        $jenis = (string) self::pick($p, ['jenis_pesanan', 'jenis', 'tipe'], '');
        $jenisLabel = Str::contains(Str::lower($jenis), ['rental', 'sewa']) ? 'Rental'
            : (Str::contains(Str::lower($jenis), ['jahit', 'custom', 'pesan']) ? 'Jahit custom' : ($jenis ?: '-'));
        $tgl = self::pick($p, ['tanggal_pesanan', 'tanggal', 'created_at']);

        return [
            'id'        => $p->getKey(),
            'pelanggan' => self::pick($p, ['pelanggan.nama', 'pelanggan.nama_pelanggan', 'pelanggan.name', 'nama_pelanggan', 'nama'], '-'),
            'telepon'   => self::pick($p, ['pelanggan.no_hp', 'pelanggan.telepon', 'pelanggan.no_telepon', 'no_hp', 'telepon'], ''),
            'pakaian'   => self::pick($p, ['produk.nama_produk', 'nama_produk', 'nama_pakaian', 'jenis_pakaian', 'pakaian'], '-'),
            'jenis'     => $jenisLabel,
            'kustom'    => $jenisLabel === 'Jahit custom',
            'ukuran'    => self::pick($p, ['ukuran'], '-'),
            'jumlah'    => self::pick($p, ['jumlah', 'qty', 'jumlah_pakaian'], 1),
            'foto'      => self::foto(self::pick($p, ['produk.foto', 'produk.gambar', 'produk.foto_produk'])),
            'status'    => $stLabel,
            'kelas'     => $stKelas,
            'key'       => $stKey,
            'tanggal'   => $tgl ? Carbon::parse($tgl)->translatedFormat('d M Y') : '-',
        ];
    }

    /** Satu produk -> array untuk kartu Collection. */
    public static function produk($pr): array
    {
        return [
            'id'       => $pr->getKey(),
            'nama'     => self::pick($pr, ['nama_produk', 'nama'], '-'),
            'kategori' => self::pick($pr, ['kategori.nama_kategori'], ''),
            'harga'    => ($h = self::pick($pr, ['harga_sewa', 'harga', 'harga_produk'])) ? self::rupiah($h) : '',
            'foto'     => self::foto(self::pick($pr, ['foto', 'gambar', 'foto_produk'])),
        ];
    }

    /** Hitungan pesanan per bulan (n bulan terakhir). */
    public static function bulanan($semua, int $n = 6): array
    {
        $labels = [];
        $vals = [];
        for ($i = $n - 1; $i >= 0; $i--) {
            $d = now()->subMonthsNoOverflow($i);
            $labels[] = $d->translatedFormat('M');
            $vals[$d->format('Y-m')] = 0;
        }
        foreach ($semua as $p) {
            $k = optional($p->created_at)->format('Y-m');
            if ($k !== null && isset($vals[$k])) $vals[$k]++;
        }
        return ['labels' => $labels, 'values' => array_values($vals)] + self::grafik(array_values($vals));
    }

    /** Titik-titik untuk grafik garis SVG (viewBox 300 x 90). */
    public static function grafik(array $v, int $w = 300, int $h = 90): array
    {
        $n = count($v);
        $max = max(max($v), 1);
        $px = 12; $py = 12;
        $pts = [];
        foreach ($v as $i => $val) {
            $x = $n > 1 ? $px + ($w - 2 * $px) * $i / ($n - 1) : $w / 2;
            $y = $h - $py - ($h - 2 * $py) * $val / $max;
            $pts[] = [round($x, 1), round($y, 1)];
        }
        $line = implode(' ', array_map(fn ($p) => $p[0] . ',' . $p[1], $pts));
        $area = 'M' . $pts[0][0] . ',' . $h . ' L' . str_replace(' ', ' L', $line) . ' L' . end($pts)[0] . ',' . $h . ' Z';
        return ['pts' => $pts, 'line' => $line, 'area' => $area];
    }
}
