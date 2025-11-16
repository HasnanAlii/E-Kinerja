<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Skp;
use Illuminate\Support\Facades\Auth;

class SkpController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->detail;

        $data = Skp::where('bidang_id', $pegawai->bidang_id)->get();

        return view('pegawai.skp.index', compact('data'));
    }
}
