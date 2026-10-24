<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /** Profil admin; tombol keluar (logout) ada di halaman ini. */
    public function show(Request $request): View
    {
        return view('admin.profil', ['admin' => $request->user('admin')]);
    }
}
