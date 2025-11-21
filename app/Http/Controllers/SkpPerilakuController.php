<?php

namespace App\Http\Controllers;

use App\Models\SkpPerilaku;
use Illuminate\Http\Request;

class SkpPerilakuController extends Controller
{
    public function store(Request $request, $skp_id)
    {
        $request->validate([
            'aspek' => 'required|string',
        ]);

        SkpPerilaku::create([
            'skp_id' => $skp_id,
            'aspek' => $request->aspek,
            'perilaku' => $request->perilaku,
            'ekspektasi' => $request->ekspektasi,
            'umpan_balik' => $request->umpan_balik,
        ]);

        return back()->with('success', 'Perilaku ASN ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data = SkpPerilaku::findOrFail($id);

        $data->update([
            'perilaku' => $request->perilaku,
            'ekspektasi' => $request->ekspektasi,
            'umpan_balik' => $request->umpan_balik,
        ]);

        return back()->with('success', 'Data perilaku diperbarui.');
    }

    public function destroy($id)
    {
        SkpPerilaku::findOrFail($id)->delete();

        return back()->with('success', 'Data perilaku dihapus.');
    }
}
