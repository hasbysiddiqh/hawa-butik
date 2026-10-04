<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'           => 'required|string|max:255',
            'whatsapp'       => 'required|string|max:20',
            'detail_pesanan' => 'required|string',
        ]);

        Pesanan::create($data);

        return redirect()->to(url('/') . '#contact')
            ->with('success', 'Pesanan Anda berhasil dikirim. Kami akan segera menghubungi Anda.');
    }
}