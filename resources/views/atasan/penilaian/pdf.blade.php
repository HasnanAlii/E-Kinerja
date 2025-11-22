<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Penilaian Kinerja - {{ $periode->nama_periode }}</title>

    <style>
        /* ATURAN HALAMAN */
        @page {
            margin: 1.5cm 1cm; /* Atas-Bawah 1.5cm, Kiri-Kanan 1cm */
            size: A4 portrait;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 11pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }

        /* INFO BOX */
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 10pt;
        }
        .info-table td {
            border: none;
            padding: 2px;
        }

        /* TABEL DATA UTAMA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 9pt; /* Font tabel sedikit lebih kecil agar muat */
            vertical-align: middle;
        }

        .data-table th {
            background-color: #F0F0F0;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        /* UTILITY CLASSES */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .italic { font-style: italic; }
        .small { font-size: 8pt; }

        /* TANDA TANGAN */
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border: none;
        }
        .signature-table td {
            border: none;
            text-align: center;
            vertical-align: top;
        }
    </style>
</head>
<body>

@php
function predikatNilai($nilai) {
    if ($nilai === null) return '-';
    if ($nilai >= 4.5) return 'Sangat Baik';
    if ($nilai >= 3.5) return 'Baik';
    if ($nilai >= 2.5) return 'Cukup';
    return 'Kurang';
}
@endphp


    {{-- 1. HEADER --}}
    <div class="header">
        <h1>LAPORAN REKAPITULASI PENILAIAN KINERJA PEGAWAI</h1>
        <h2>PERIODE: {{ strtoupper($periode->nama_periode) }}</h2>
    </div>

    {{-- 2. INFO TAMBAHAN --}}
    <table class="info-table">
        <tr>
            <td width="120px" class="bold">UNIT KERJA</td>
            <td>: {{ Auth::user()->atasan->bidang->nama_bidang ?? 'SEMUA BIDANG' }}</td>
            <td width="100px" class="bold text-right">DICETAK</td>
            <td width="140px" class="text-right">: {{ now()->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    {{-- 3. TABEL PENILAIAN --}}
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" width="30px">NO</th>
                <th rowspan="2">NAMA PEGAWAI</th>
                <th rowspan="2" width="80px">JABATAN</th>
                <th colspan="6">ASPEK PERILAKU KERJA</th>
                <th rowspan="2" width="70px">PREDIKAT</th>
            </tr>
            <tr style="font-size: 8pt;">
                <th width="35px">Orientasi<br>Pelayanan</th>
                <th width="35px">Akuntabel</th>
                <th width="35px">Kompeten</th>
                <th width="35px">Harmonis</th>
                <th width="35px">Loyal</th>
                <th width="35px">Adaptif</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($pegawaiAktif as $i => $p)
                @php
                    // Ambil data penilaian berdasarkan pegawai & periode
                    $n = \App\Models\Penilaian::where('pegawai_id', $p->id)
                            ->where('periode_id', $periode->id)
                            ->first();
                @endphp

                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    
                    <td>
                        <span class="bold uppercase">{{ $p->user->name }}</span><br>
                        {{-- <span class="small">{{ $p->nip ?? '-' }}</span> --}}
                    </td>

                    <td class="text-center small">{{ $p->jabatan ?? '-' }}</td>

                    @if($n)
                    <td class="text-center">{{ predikatNilai($n->perilaku) }}</td>       
                    <td class="text-center">{{ predikatNilai($n->kedisiplinan) }}</td>   
                    <td class="text-center">{{ predikatNilai($n->komunikasi) }}</td>     
                    <td class="text-center">{{ predikatNilai($n->kerja_sama) }}</td>    
                    <td class="text-center">{{ predikatNilai($n->tanggung_jawab) }}</td> 
                    <td class="text-center">{{ predikatNilai($n->produktivitas) }}</td>  
                    <td class="text-center small">{{ $n->kategori ?? '-' }}</td>



 
                    @else
                        <td colspan="8" class="text-center italic text-gray-500" style="background-color: #fff5f5;">
                            Belum Dinilai
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center italic" style="padding: 20px;">
                        Tidak ada data pegawai pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 4. TANDA TANGAN --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td width="60%"></td> {{-- Spasi Kosong --}}
                <td width="40%">
                    Cianjur, {{ now()->translatedFormat('d F Y') }}<br>
                    Pejabat Penilai Kinerja,<br>
                    <br><br><br><br>
                    <u class="bold uppercase">{{ Auth::user()->name }}</u><br>
                    NIP. {{ Auth::user()->atasan->nip ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>