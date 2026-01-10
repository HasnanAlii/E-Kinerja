<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Kehadiran - {{ $pegawai->user->name }}</title>

    <style>
        @page { margin: 1.5cm 1cm; size: A4 portrait; }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
        }

        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 12pt; font-weight: bold; margin: 0; }
        .header h2 { font-size: 11pt; font-weight: bold; margin: 2px 0; }

        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 3px; }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 9pt;
            vertical-align: top;
        }
        .data-table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .signature-section { margin-top: 40px; }
    </style>
</head>
<body>

<div class="header">
    <h1>LAPORAN REKAPITULASI KEHADIRAN PEGAWAI</h1>
    {{-- <h2>{{ strtoupper($pegawai->user->name) }}</h2> --}}
    {{-- <div>PERIODE: {{ strtoupper($periode->nama_periode) }}</div> --}}
</div>
<table class="info-table">
    <tr>
        <td width="120"><strong>Nama</strong></td>
        <td width="200">: {{ $pegawai->user->name ?? '-' }}</td>

        <td width="120"><strong>NIP</strong></td>
        <td width="200">: {{ $pegawai->nip ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Jabatan</strong></td>
        <td>: {{ $pegawai->jabatan ?? '-' }}</td>

        <td><strong>Unit Kerja</strong></td>
        <td>: {{ $pegawai->bidang->nama_bidang ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Tanggal Cetak</strong></td>
        <td>: {{ now()->translatedFormat('d F Y') }}</td>

        <td></td>
        <td></td>
    </tr>
</table>


<h3>REKAP TANGGAL BERDASARKAN JENIS KEHADIRAN</h3>

<table class="data-table">
    <thead>
        <tr>
            <th width="20%">Hadir</th>
            <th width="20%">Izin</th>
            <th width="20%">Sakit</th>
            <th width="20%">Cuti</th>
            <th width="20%">Alpha</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center">
                {{ $rekap['hadir']->count() }}
            </td>
            <td class="text-center">
                {{ $rekap['izin']->count() }}
            </td>
            <td class="text-center">
                {{ $rekap['sakit']->count() }}
            </td>
            <td class="text-center">
                {{ $rekap['cuti']->count() }}
            </td>
            <td class="text-center">
                {{ $rekap['alpha']->count() }}
            </td>
        </tr>
    </tbody>
</table>

<h3>DETAIL KEHADIRAN HARIAN</h3>

<table class="data-table">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="20%">Tanggal</th>
            <th width="20%">Hari</th>
            <th width="20%">Jenis Kehadiran</th>
 
        </tr>
    </thead>
    <tbody>
        @foreach($detail as $i => $d)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d-m-Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('l') }}</td>
            <td style="text-transform: uppercase; font-weight: bold;">
                {{ $d->jenis }}
            </td>
      
        </tr>
        @endforeach
    </tbody>
</table>

<div class="signature-section">
    <table width="100%">
        <tr>
            <td width="60%"></td>
            <td width="40%" align="center">
                Cianjur, {{ now()->translatedFormat('d F Y') }}<br>
                Atasan Langsung,<br><br><br><br>
                <strong>{{ Auth::user()->name }}</strong><br>
                NIP. {{ Auth::user()->atasan->nip ?? '-' }}
            </td>
        </tr>
    </table>
</div>

</body>
</html>
