<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Kehadiran Karyawan PT Pou Yuen Indonesia</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm;
            size: A4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #111827;
            line-height: 1.4;
        }
        .letterhead {
            width: 100%;
            border-bottom: 3px double #111827;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .company-name {
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #111827;
            text-align: center;
            margin: 0;
            text-transform: uppercase;
        }
        .company-sub {
            font-size: 9.5pt;
            font-weight: bold;
            color: #374151;
            text-align: center;
            margin: 2px 0;
        }
        .company-address {
            font-size: 8.5pt;
            color: #4b5563;
            text-align: center;
            margin: 2px 0 0 0;
        }
        .doc-title-box {
            text-align: center;
            margin: 14px 0 16px 0;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-subtitle {
            font-size: 9pt;
            color: #374151;
            margin-top: 4px;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9pt;
        }
        .meta-info td {
            padding: 2px 0;
        }
        table.formal-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.formal-table th,
        table.formal-table td {
            border: 1px solid #374151;
            padding: 6px 6px;
            font-size: 8.8pt;
        }
        table.formal-table th {
            background-color: #e5e7eb;
            color: #111827;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .summary-row td {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .signature-wrap {
            width: 100%;
            margin-top: 34px;
        }
        .signature-wrap td {
            vertical-align: top;
            font-size: 9pt;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT FORMAL PT POU YUEN INDONESIA --}}
    <div class="letterhead">
        <p class="company-name">PT. POU YUEN INDONESIA</p>
        <p class="company-sub">DIVISI MANAJEMEN SUMBER DAYA MANUSIA &amp; PENGAWASAN KEHADIRAN</p>
        <p class="company-address">
            Jl. Raya Bandung - Cianjur KM. 7, Desa Sukasirna, Kec. Sukaluyu, Kabupaten Cianjur, Jawa Barat 43284<br>
            Telp: (0263) 2911888 &bull; Email: hr-monitoring@pouyuen.co.id
        </p>
    </div>

    <div class="doc-title-box">
        <p class="doc-title">LAPORAN REKAPITULASI KEHADIRAN KARYAWAN</p>
        <p class="doc-subtitle">
            Periode Tanggal: <strong>{{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }}</strong>
            s/d <strong>{{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}</strong>
        </p>
    </div>

    <table class="meta-info">
        <tr>
            <td width="18%"><strong>Tanggal Cetak</strong></td>
            <td width="32%">: {{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
            <td width="18%"><strong>Total Karyawan</strong></td>
            <td width="32%">: {{ $rekap->count() }} Orang</td>
        </tr>
    </table>

    <table class="formal-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="14%">NIK Karyawan</th>
                <th width="23%">Nama Karyawan</th>
                <th width="18%">Departemen / Divisi</th>
                <th width="14%">Jabatan</th>
                <th width="5%">Hadir</th>
                <th width="5%">Izin</th>
                <th width="5%">Sakit</th>
                <th width="5%">Cuti</th>
                <th width="6%">Alpha</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekap as $i => $r)
                @php
                    $deptDiv = trim(($r->departemen !== '-' ? $r->departemen : '') . (($r->departemen !== '-' && $r->divisi !== '-') ? ' / ' : '') . ($r->divisi !== '-' ? $r->divisi : ''));
                    if ($deptDiv === '') $deptDiv = '-';
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $r->nik_karyawan }}</td>
                    <td class="font-bold">{{ $r->nama }}</td>
                    <td>{{ $deptDiv }}</td>
                    <td>{{ $r->jabatan }}</td>
                    <td class="text-center">{{ $r->hadir }}</td>
                    <td class="text-center">{{ $r->izin }}</td>
                    <td class="text-center">{{ $r->sakit }}</td>
                    <td class="text-center">{{ $r->cuti }}</td>
                    <td class="text-center">{{ $r->alpha }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 14px;">Tidak ada data kehadiran pada rentang tanggal ini.</td>
                </tr>
            @endforelse
            @if ($rekap->isNotEmpty())
                <tr class="summary-row">
                    <td colspan="5" class="text-center">TOTAL KESELURUHAN</td>
                    <td class="text-center">{{ $rekap->sum('hadir') }}</td>
                    <td class="text-center">{{ $rekap->sum('izin') }}</td>
                    <td class="text-center">{{ $rekap->sum('sakit') }}</td>
                    <td class="text-center">{{ $rekap->sum('cuti') }}</td>
                    <td class="text-center">{{ $rekap->sum('alpha') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="signature-wrap">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="text-center">
                Cianjur, {{ now()->translatedFormat('d F Y') }}<br>
                Mengetahui,<br>
                <strong>Atasan / Supervisor Pengawas</strong>
                <br><br><br><br>
                <u><strong>{{ Auth::user()->name ?? 'Supervisor PT Pou Yuen' }}</strong></u>
            </td>
        </tr>
    </table>

</body>
</html>
