<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penilaian Pegawai Aktif</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #777;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #e8eefc;
            font-weight: bold;
            text-align: center;
        }
        .small {
            font-size: 11px;
            color: #777;
        }
        .header {
            text-align:center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN PENILAIAN PEGAWAI AKTIF</h2>
        <p class="small">Diunduh pada: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Jabatan</th>
                <th>Nilai Kinerja</th>
                <th>Status SKP</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pegawaiAktif as $i => $p)
            <tr>
                <td style="text-align:center">{{ $i+1 }}</td>
                <td>{{ $p->user->name ?? '-' }}</td>
                <td>{{ $p->jabatan ?? '-' }}</td>
                <td style="text-align:center">{{ $p->nilai_kinerja ?? '-' }}</td>
                <td style="text-align:center">
                    {{ $p->skp_status ?? 'Belum Ada' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
