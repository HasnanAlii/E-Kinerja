<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use Illuminate\Http\Request;

class BidangController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:admin');
    // }

    public function index()
    {
        $data = Bidang::paginate(20);

        return view('admin.bidang.index', compact('data'));
    }

    public function create()
    {
        return view('admin.bidang.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nama_bidang' => 'required']);

        Bidang::create(['nama_bidang' => $request->nama_bidang]);

        return redirect()->route('admin.bidang.index')
            ->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = Bidang::findOrFail($id);

        return view('admin.bidang.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_bidang' => 'required']);

        Bidang::findOrFail($id)->update([
            'nama_bidang' => $request->nama_bidang
        ]);

        return back()->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Bidang::findOrFail($id)->delete();

        return back()->with('success', 'Bidang berhasil dihapus.');
    }
}
