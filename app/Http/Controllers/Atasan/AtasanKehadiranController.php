<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\IzinSakit;
use App\Models\Kehadiran;
use App\Models\Pegawai;
use App\Models\PegawaiDetail;
use App\Models\PeriodePenilaian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AtasanKehadiranController extends Controller
{
    /**
     * Tampilkan daftar kehadiran dengan filter opsional
     */
    public function index(Request $request)
    {
        $query = Kehadiran::with('pegawai.user');

        // Default: hanya hari ini jika filter tanggal tidak dipakai
        if (!$request->filled('tanggal_dari') && !$request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', Carbon::today());
        }

        // Filter pegawai
        if ($request->filled('pegawai_id')) {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        // Filter tanggal
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal', '>=', Carbon::parse($request->tanggal_dari)->format('Y-m-d'));
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal', '<=', Carbon::parse($request->tanggal_sampai)->format('Y-m-d'));
        }

        $data = $query->orderBy('tanggal', 'desc')->paginate(25)->withQueryString();

        $pegawais = PegawaiDetail::select('pegawai_details.*')
            ->join('users', 'users.id', '=', 'pegawai_details.user_id')
            ->orderBy('users.name')
            ->get();

        return view('atasan.kehadiran.index', compact('data', 'pegawais'));
    }

       public function indexx(Request $request)
    {
        $query = Kehadiran::with('pegawai.user', 'pegawai.bidang');

        // Default: hanya hari ini jika filter tanggal tidak dipakai
        if (!$request->filled('tanggal_dari') && !$request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', Carbon::today());
        }

        // ======================
        // 🔍 FILTER BIDANG
        // ======================
        if ($request->filled('bidang_id')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('bidang_id', $request->bidang_id);
            });
        }

        // Filter tanggal
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal', '>=', Carbon::parse($request->tanggal_dari)->format('Y-m-d'));
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal', '<=', Carbon::parse($request->tanggal_sampai)->format('Y-m-d'));
        }

        $data = $query->orderBy('tanggal', 'desc')
                    ->paginate(25)
                    ->withQueryString();

        // Untuk dropdown bidang
        $bidangs = Bidang::orderBy('nama_bidang')->get();

        return view('admin.kehadiran.index', compact('data', 'bidangs'));
    }



    /**
     * Tampilkan detail satu record kehadiran
     */
    public function show($id)
    {
        $row = Kehadiran::with('pegawai.user')->findOrFail($id);

        return view('atasan.kehadiran.show', compact('row'));
    }

    /**
     * Update (koreksi) check_in / check_out
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'check_in' => 'nullable|date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $row = Kehadiran::findOrFail($id);

        // Simpan perubahan
        $row->check_in = $request->check_in ? Carbon::parse($request->check_in) : $row->check_in;
        $row->check_out = $request->check_out ? Carbon::parse($request->check_out) : $row->check_out;

        // jika model ada kolom 'keterangan' gunakan, jika tidak, harap tambahkan atau simpan ke kolom lain
        if ($request->filled('keterangan') && array_key_exists('keterangan', $row->getAttributes())) {
            $row->keterangan = $request->keterangan;
        }

        // optional: catat siapa yang mengoreksi; butuh kolom verified_by di table
        if (array_key_exists('verified_by', $row->getAttributes())) {
            $row->verified_by = Auth::id();
        }

        $row->save();

        return redirect()->route('atasan.kehadiran.show', $row->id)
            ->with('success', 'Data kehadiran berhasil diperbarui.');
    }

    /**
     * Jika ingin menandai absen manual (opsional) – contoh endpoint untuk menambah record
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tanggal' => 'required|date',
            'check_in' => 'nullable|date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $data = Kehadiran::create([
            'pegawai_id' => $request->pegawai_id,
            'tanggal' => Carbon::parse($request->tanggal)->format('Y-m-d'),
            'check_in' => $request->check_in ? Carbon::parse($request->check_in) : null,
            'check_out' => $request->check_out ? Carbon::parse($request->check_out) : null,
            'keterangan' => $request->keterangan ?? null,
            // 'verified_by' => auth()->id() // jika kolom ada
        ]);

        return redirect()->route('atasan.kehadiran.index')
            ->with('success', 'Data kehadiran manual berhasil ditambahkan.');
    }


public function cetakKehadiran(Request $request)
{
    $pegawaiId = $request->pegawai_id;

    $pegawai = PegawaiDetail::with(['user','bidang'])->findOrFail($pegawaiId);

    $query = Kehadiran::where('pegawai_id', $pegawaiId);

    if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
        $query->whereBetween('tanggal', [
            $request->tanggal_dari,
            $request->tanggal_sampai
        ]);
    }

    $detail = $query->orderBy('tanggal')->get();

    $rekap = [
        'hadir' => $detail->where('jenis','hadir'),
        'izin'  => $detail->where('jenis','izin'),
        'sakit' => $detail->where('jenis','sakit'),
        'cuti'  => $detail->where('jenis','cuti'),
        'alpha' => $detail->where('jenis','alpha'),
    ];

    $pdf = Pdf::loadView('atasan.kehadiran.pdf', compact('pegawai','detail','rekap'))
        ->setPaper('a4','portrait');

    return $pdf->stream("Laporan_Kehadiran_{$pegawai->user->name}.pdf");
}


public function cetakadmin($pegawaiId)
{
    // Pegawai yang dipilih dari tabel (bukan user login)
    $pegawai = PegawaiDetail::with(['user','bidang'])->findOrFail($pegawaiId);

    // Periode aktif
    $periode = PeriodePenilaian::where('status_aktif', true)->first();

    // Ambil seluruh kehadiran dalam periode aktif
    $detail = Kehadiran::where('pegawai_id', $pegawaiId)
   
        ->get();

    // Kelompokkan per jenis (list tanggal)
    $rekap = [
        'hadir' => $detail->where('jenis', 'hadir'),
        'izin'  => $detail->where('jenis', 'izin'),
        'sakit' => $detail->where('jenis', 'sakit'),
        'cuti'  => $detail->where('jenis', 'cuti'),
        'alpha' => $detail->where('jenis', 'alpha'),
    ];

    $pdf = Pdf::loadView('admin.kehadiran.pdf', compact(
        'pegawai',
        'periode',
        'detail',
        'rekap'
    ))->setPaper('a4','portrait');

    return $pdf->stream("Laporan_Kehadiran_{$pegawai->user->name}.pdf");
}



}
