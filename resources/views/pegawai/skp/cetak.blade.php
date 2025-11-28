<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SKP - {{ $data->pegawai->user->name }}</title>

    <style>
        body { font-family: Arial, sans-serif; font-size: 11.5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 5px; vertical-align: top; }

        h2, h3 {
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            margin: 4px 0;
        }

        .no-border td, .no-border th { border: none !important; }
        .bold { font-weight: bold; }
        .center { text-align: center; }
        .uppercase { text-transform: uppercase; }
        .gray-bg { background: #F2F2F2; }

        /* MULTI-PAGE FIX for DOMPDF */
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr, td, th { page-break-inside: avoid; }
        @page { margin: 25px 20px; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <h2>Evaluasi Kinerja Pegawai</h2>
    <h3>Pendekatan Hasil Kerja Kuantitatif</h3>
    <p class="center">PERIODE: {{ $data->periode }}</p>
    <p>PERIODE PENILAIAN: 
        {{ \Carbon\Carbon::parse($data->tanggal_mulai)->format('d M Y') }}
        SD
        {{ \Carbon\Carbon::parse($data->tanggal_selesai)->format('d M Y') }}
    </p>

    {{-- IDENTITAS PEGAWAI & ATASAN --}}
    <table>
        <tr class="gray-bg bold center">
            <th>No</th>
            <th colspan="2">Pegawai yang Dinilai</th>
            <th>No</th>
            <th colspan="2">Atasan Penilai Kinerja</th>
        </tr>

        @php $atasan = $data->pegawai->atasan; @endphp

        <tr>
            <td class="center">1</td>
            <td>Nama</td>
            <td class="uppercase bold">{{ $data->pegawai->user->name }}</td>

            <td class="center">1</td>
            <td>Nama</td>
            <td class="uppercase bold">{{ $atasan->user->name ?? '-' }}</td>
        </tr>

        <tr>
            <td class="center">2</td>
            <td>NIP</td>
            <td>{{ $data->pegawai->nip ?? '-' }}</td>

            <td class="center">2</td>
            <td>NIP</td>
            <td>{{ $atasan->nip ?? '-' }}</td>
        </tr>

        <tr>
            <td class="center">3</td>
            <td>Pangkat / Gol</td>
            <td>{{ $data->pegawai->golongan ?? '-' }}</td>

            <td class="center">3</td>
            <td>Pangkat / Gol</td>
            <td>{{ $atasan->golongan ?? '-' }}</td>
        </tr>

        <tr>
            <td class="center">4</td>
            <td>Jabatan</td>
            <td>{{ $data->pegawai->jabatan ?? '-' }}</td>

            <td class="center">4</td>
            <td>Jabatan</td>
            <td>{{ $atasan->jabatan ?? '-' }}</td>
        </tr>

        <tr>
            <td class="center">5</td>
            <td>Unit Kerja</td>
            <td>{{ $data->pegawai->bidang->nama_bidang ?? '-' }}</td>

            <td class="center">5</td>
            <td>Unit Kerja</td>
            <td>{{ $atasan->bidang->nama_bidang ?? '-' }}</td>
        </tr>
    </table>

    {{-- CAPAIAN ORGANISASI --}}
    <table>
        <tr class="gray-bg bold">
            <td>CAPAIAN KINERJA ORGANISASI:</td>
        </tr>
        <tr>
            <td>{{ $data->capaian_kinerja_organisasi ?? '-' }}</td>
        </tr>
    </table>

    {{-- POLA DISTRIBUSI --}}
    <table>
        <tr class="gray-bg bold">
            <td>POLA DISTRIBUSI:</td>
        </tr>
        <tr>
            <td>{{ $data->pola_distribusi ?? '-' }}</td>
        </tr>
    </table>

    {{-- HASIL KERJA --}}
    <div class="bold" style="margin-top: 10px; margin-bottom: 4px;">HASIL KERJA</div>

    <table>
        <thead class="bold center gray-bg">
            <tr>
                <td>No</td>
                <td>Rencana Hasil Kerja Pimpinan</td>
                <td>Rencana Hasil Kerja</td>
                <td>Aspek</td>
                <td>Indikator</td>
                <td>Target</td>
                <td>Realisasi</td>
                <td>Umpan Balik</td>
            </tr>
        </thead>

        <tbody>
            <tr class="gray-bg bold">
                <td colspan="8">UTAMA</td>
            </tr>

            @php
                $grouped = $data->hasilKerja->groupBy('rhk');
                $no = 1;
            @endphp

            @forelse ($grouped as $rhk => $group)
                @php 
                    $rowspan = $group->count(); 
                    $first = $group->first();
                @endphp

                <tr>
                    <td rowspan="{{ $rowspan }}" class="center">{{ $no++ }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $first->rhk_pimpinan }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $first->rhk }}</td>
                    <td class="center">{{ $first->aspek }}</td>
                    <td>{{ $first->indikator_kinerja }}</td>
                    <td class="center">{{ $first->target }}</td>
                    <td class="center">{{ $first->realisasi ?? '-' }}</td>
                    <td>{{ $first->umpan_balik ?? '-' }}</td>
                </tr>

                @foreach ($group->skip(1) as $item)
                <tr>
                    <td class="center">{{ $item->aspek }}</td>
                    <td>{{ $item->indikator_kinerja }}</td>
                    <td class="center">{{ $item->target }}</td>
                    <td class="center">{{ $item->realisasi ?? '-' }}</td>
                    <td>{{ $item->umpan_balik ?? '-' }}</td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="8" class="center italic">Belum ada data hasil kerja.</td>
                </tr>
            @endforelse

            <tr class="gray-bg bold">
                <td colspan="8">TAMBAHAN</td>
            </tr>
            <tr>
                <td colspan="8" class="center italic">-</td>
            </tr>
        </tbody>
    </table>

    {{-- RATING & PREDIKAT --}}
    <table>
        <tr>
            <td class="bold gray-bg">RATING HASIL KERJA</td>
            <td>{{ $data->rating ?? '-' }}</td>
        </tr>
        <tr>
            <td class="bold gray-bg">PREDIKAT KINERJA</td>
            <td class="uppercase">{{ $data->predikat ?? '-' }}</td>
        </tr>
    </table>

    {{-- TANDA TANGAN --}}
    <table class="no-border center" style="margin-top: 45px;">
        <tr class="no-border">
            <td class="no-border">
                Cianjur, {{ now()->translatedFormat('d F Y') }}<br>
                <b>Pegawai Yang Dinilai</b><br><br><br><br>
                <b class="uppercase">{{ $data->pegawai->user->name }}</b><br>
                NIP. {{ $data->pegawai->nip ?? '-' }}
            </td>

            <td class="no-border">
                Cianjur, {{ now()->translatedFormat('d F Y') }}<br>
                <b>Atasan Langsung</b><br><br><br><br>
                <b class="uppercase">{{ $atasan->user->name ?? '-' }}</b><br>
                NIP. {{ $atasan->nip ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>
