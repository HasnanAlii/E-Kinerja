<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Data Karyawan (T1) - {{ $pegawai->user->name }}</title>
    <style>
        @page {
            margin: 1.2cm 1.4cm;
            size: A4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #1f2937;
            line-height: 1.35;
        }
        .top-meta {
            width: 100%;
            margin-bottom: 14px;
            font-size: 8.5pt;
            color: #374151;
        }
        .top-meta td {
            padding: 0;
        }
        .profile-layout {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .photo-col {
            width: 155px;
            vertical-align: top;
            padding-right: 18px;
            text-align: center;
        }
        .photo-box {
            width: 145px;
            height: 180px;
            border: 1px solid #9ca3af;
            background-color: #f3f4f6;
            text-align: center;
            overflow: hidden;
        }
        .photo-box img {
            width: 145px;
            height: 180px;
            object-fit: cover;
        }
        .photo-placeholder {
            padding-top: 72px;
            font-size: 9pt;
            color: #6b7280;
        }
        .status-text {
            margin-top: 8px;
            font-size: 9pt;
            font-weight: bold;
            color: #111827;
        }
        .info-col {
            vertical-align: top;
        }
        .section-heading {
            font-size: 10.5pt;
            font-weight: bold;
            color: #111827;
            padding-bottom: 5px;
            border-bottom: 2px solid #111827;
            margin-bottom: 6px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 4px;
            border-bottom: 1px solid #d1d5db;
            font-size: 9.5pt;
        }
        .info-label {
            width: 34%;
            color: #374151;
        }
        .info-value {
            width: 66%;
            color: #111827;
        }
        .info-value-bold {
            font-weight: bold;
            text-transform: uppercase;
        }
        .rekap-summary {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 18px 0;
        }
        .rekap-summary th, .rekap-summary td {
            border: 1px solid #d1d5db;
            padding: 5px 8px;
            text-align: center;
            font-size: 8.5pt;
        }
        .rekap-summary th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .attendance-table th, .attendance-table td {
            border: 1px solid #9ca3af;
            padding: 5px 7px;
            font-size: 8.8pt;
            text-align: left;
        }
        .attendance-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #111827;
        }
        .muted-italic {
            color: #6b7280;
            font-style: italic;
        }
        .footer-meta {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            font-size: 8pt;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
        }
    </style>
</head>
<body>

@php
    $photoPath = null;
    if (!empty($pegawai->user->profile_photo)) {
        $candidate = storage_path('app/public/' . $pegawai->user->profile_photo);
        if (file_exists($candidate)) {
            $photoPath = $candidate;
        }
    }
    $deptDivisi = trim(($pegawai->departemen ?? '') . (($pegawai->departemen && $pegawai->divisi) ? ' / ' : '') . ($pegawai->divisi ?? ''));
    if ($deptDivisi === '') {
        $deptDivisi = $pegawai->bidang->nama_bidang ?? '-';
    }
    $nikKaryawan = $pegawai->nik_karyawan ?? $pegawai->nip ?? ($pegawai->user->nik ?? '-');
@endphp

<table class="top-meta">
    <tr>
        <td align="left">{{ now()->format('n/j/y, g:i A') }}</td>
        <td align="center"><strong>Data Karyawan (T1) — PT Pou Yuen Indonesia</strong></td>
        <td align="right"></td>
    </tr>
</table>

<table class="profile-layout">
    <tr>
        <td class="photo-col">
            <div class="photo-box">
                @if ($photoPath)
                    <img src="{{ $photoPath }}" alt="Foto Karyawan">
                @else
                    <div class="photo-placeholder">Foto Karyawan</div>
                @endif
            </div>
            <div class="status-text">{{ $pegawai->status ?? 'Masih Bekerja' }}</div>
        </td>
        <td class="info-col">
            <div class="section-heading">Informasi Umum</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Nama</td>
                    <td class="info-value info-value-bold">{{ strtoupper($pegawai->user->name ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Jenis Kelamin</td>
                    <td class="info-value info-value-bold">{{ strtoupper($pegawai->user->jenis_kelamin ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Departemen / Divisi</td>
                    <td class="info-value">{{ $deptDivisi }}</td>
                </tr>
                <tr>
                    <td class="info-label">Jabatan</td>
                    <td class="info-value">{{ $pegawai->jabatan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">NIK Karyawan</td>
                    <td class="info-value">{{ $nikKaryawan }}</td>
                </tr>
                <tr>
                    <td class="info-label">Status</td>
                    <td class="info-value">{{ $pegawai->status ?? 'Masih Bekerja' }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="section-heading" style="margin-top: 10px;">
    Data Kehadiran ({{ $detail->count() }} Hari Terakhir)
</div>

<table class="attendance-table">
    <thead>
        <tr>
            <th width="22%">Tanggal</th>
            <th width="24%">Check-In</th>
            <th width="24%">Check-Out</th>
            <th width="15%">Kode</th>
            <th width="15%">Lembur</th>
        </tr>
    </thead>
    <tbody>
        @forelse($detail as $d)
            @php
                $isHadir = ($d->jenis === 'hadir');
                $checkIn = $d->check_in ? \Carbon\Carbon::parse($d->check_in)->format('H:i:s') : ($isHadir ? '-' : '00:00');
                $checkOut = $d->check_out ? \Carbon\Carbon::parse($d->check_out)->format('H:i:s') : ($isHadir ? null : '00:00');
                $kode = $isHadir ? 'SYNC' : strtoupper($d->jenis ?? 'OFF');
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('Y/m/d') }}</td>
                <td>{{ $checkIn }}</td>
                <td>
                    @if ($isHadir && !$checkOut)
                        <span class="muted-italic">--- belum pulang</span>
                    @else
                        {{ $checkOut }}
                    @endif
                </td>
                <td>{{ $kode }}</td>
                <td>-</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 14px;" class="muted-italic">
                    Belum ada riwayat kehadiran tercatat.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<table class="footer-meta">
    <tr>
        <td align="left">Sistem Monitoring Kehadiran Otomatis — PT Pou Yuen Indonesia</td>
        <td align="right">1/1</td>
    </tr>
</table>

</body>
</html>
