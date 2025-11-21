<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penilaian Pegawai - Periode Aktif</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
        }
        .sub {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #555;
            padding: 6px 8px;
        }
        th {
            background: #e8eefc;
            text-align: center;
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <h2>LAPORAN PENILAIAN PEGAWAI</h2>
    <div class="sub">
        Periode: <strong>{{ $periode->nama_periode ?? '-' }}</strong><br>
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Jabatan</th>
                <th>Nilai Akhir</th>
                <th>Kategori</th>
                <th>Status SKP</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($pegawaiAktif as $i => $p)

            {{-- ambil penilaian periode aktif --}}
            @php
                $nilai = \App\Models\Penilaian::where('pegawai_id', $p->id)
                    ->where('periode_id', $periode->id)
                    ->where('status', 1)
                    ->first();
            @endphp

            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $p->user->name }}</td>
                <td>{{ $p->jabatan ?? '-' }}</td>

                {{-- Nilai --}}
                <td class="center">{{ $nilai->nilai_total ?? '-' }}</td>

                {{-- Kategori --}}
                <td class="center">{{ $nilai->kategori ?? '-' }}</td>

                {{-- SKP --}}
                <td class="center">
                    {{ $p->skp_status ?? 'Belum Ada' }}
                </td>
            </tr>

            @endforeach
        </tbody>
    </table>

</body>
</html>
