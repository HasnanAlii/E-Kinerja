<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;

class AktivitasController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:admin');
    // }

    public function index()
    {
        $data = Aktivitas::with(['pegawai.user'])
            ->latest()
            ->paginate(30);

        return view('admin.aktivitas.index', compact('data'));
    }

    public function show($id)
    {
        $data = Aktivitas::with('pegawai.user')->findOrFail($id);

        return view('admin.aktivitas.show', compact('data'));
    }
}
