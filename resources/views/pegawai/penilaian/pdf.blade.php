<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SKP - {{ $skp->pegawai->user->name }}</title>

    <style>
        body { font-family: Arial, sans-serif; font-size: 11.5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }

        h2, h3 {
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            margin: 4px 0;
        }

        .no-border td { border: none !important; }
        .bold { font-weight: bold; }
        .center { text-align: center; }
        .uppercase { text-transform: uppercase; }
        .gray-bg { background: #F2F2F2; }
        .mt-20 { margin-top: 18px; }

        /* MULTI PAGE FIX */
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr, td, th { page-break-inside: avoid; }
        @page { margin: 20px 20px; }

    </style>
</head>
<body>

    {{-- HEADER --}}
    <h2>Evaluasi Kinerja Pegawai</h2>
    <h3>Pendekatan Hasil Kerja Kuantitatif</h3>
    <p class="center">PERIODE: {{ $skp->periode }}</p>
    <div style="text-align: right; width: 100%; margin-bottom: 5px;">
        <p>
            PENILAIAN:
            {{ \Carbon\Carbon::parse($skp->tanggal_mulai)->format('d M Y') }}
            SD
            {{ \Carbon\Carbon::parse($skp->tanggal_selesai)->format('d M Y') }}
        </p>
    </div>

    {{-- IDENTITAS --}}
    <table>
        <tr class="gray-bg bold center">
            <th>No</th><th colspan="2">Pegawai yang Dinilai</th>
            <th>No</th><th colspan="2">Atasan Penilai Kinerja</th>
        </tr>

        @php $atasan = $skp->pegawai->atasan; @endphp
        
        <tr>
            <td class="center">1</td><td>Nama</td>
            <td class="uppercase bold">{{ $skp->pegawai->user->name }}</td>

            <td class="center">1</td><td>Nama</td>
            <td class="uppercase bold">{{ $atasan->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="center">2</td><td>NIP</td>
            <td>{{ $skp->pegawai->nip ?? '-' }}</td>

            <td class="center">2</td><td>NIP</td>
            <td>{{ $atasan->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td class="center">3</td><td>Pangkat/Gol</td>
            <td>{{ $skp->pegawai->golongan ?? '-' }}</td>

            <td class="center">3</td><td>Pangkat/Gol</td>
            <td>{{ $atasan->golongan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="center">4</td><td>Jabatan</td>
            <td>{{ $skp->pegawai->jabatan ?? '-' }}</td>

            <td class="center">4</td><td>Jabatan</td>
            <td>{{ $atasan->jabatan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="center">5</td><td>Unit Kerja</td>
            <td>{{ $skp->pegawai->bidang->nama_bidang ?? '-' }}</td>

            <td class="center">5</td><td>Unit Kerja</td>
            <td>{{ $atasan->bidang->nama_bidang ?? '-' }}</td>
        </tr>
    </table>

    {{-- CAPAIAN ORGANISASI --}}
    <table class="mt-20">
        <tr><td class="bold gray-bg">CAPAIAN KINERJA ORGANISASI:</td></tr>
        <tr><td>{{ $skp->capaian_kinerja_organisasi ?? '-' }}</td></tr>
    </table>

    {{-- POLA DISTRIBUSI --}}
    <table>
        <tr><td class="bold gray-bg">POLA DISTRIBUSI:</td></tr>
        <tr><td>{{ $skp->pola_distribusi ?? '-' }}</td></tr>
    </table>

    
    {{-- HASIL KERJA --}}
    <div class="bold" style="margin-top: 10px; margin-bottom: 2px;">HASIL KERJA</div>

    <table>
        <thead class="bold center gray-bg">
            <tr>
                <td>No</td>
                <td>Rencana Hasil Kerja Pimpinan</td>
                <td>Rencana Hasil Kerja</td>
                <td>Aspek</td>
                <td>Indikator Kinerja Individu</td>
                <td>Target</td>
                <td>Realisasi</td>
                <td>Umpan Balik</td>
            </tr>
        </thead>

        <tbody>
            <tr class="bold gray-bg"><td colspan="8">UTAMA</td></tr>

            @php
                $utama = $skp->hasilKerja->where('jenis','Utama')->groupBy('rhk');
                $no = 1;
            @endphp

            @forelse($utama as $rhk => $group)
                @php $rowspan = $group->count(); $first = $group->first(); @endphp
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

                @foreach($group->skip(1) as $item)
                <tr>
                    <td class="center">{{ $item->aspek }}</td>
                    <td>{{ $item->indikator_kinerja }}</td>
                    <td class="center">{{ $item->target }}</td>
                    <td class="center">{{ $item->realisasi }}</td>
                    <td>{{ $item->umpan_balik }}</td>
                </tr>
                @endforeach
            @empty
                <tr><td colspan="8" class="center italic">Belum ada data utama</td></tr>
            @endforelse

            <tr class="bold gray-bg"><td colspan="8">TAMBAHAN</td></tr>
            <tr><td colspan="8" class="center italic">-</td></tr>

        </tbody>
    </table>


    {{-- PERILAKU KERJA --}}
    @if ($penilaian)
<div class="bold" style="margin-top: 10px; margin-bottom: 2px;">PERILAKU KERJA</div>
        @php
        function toKategori($nilai) {
            if ($nilai >= 4.5) return 'Sangat Baik';
            if ($nilai >= 3.5) return 'Baik';
            if ($nilai >= 2.5) return 'Cukup';
            return 'Kurang';
        }
        @endphp

    <table>
        <thead class="bold center gray-bg">
            <tr>
                <td>No</td><td>Aspek</td><td>Nilai</td>
            </tr>
        </thead>
        <tbody>
        <tr><td class="center">1</td><td>Integritas</td><td class="center">{{ toKategori($penilaian->perilaku) }}</td></tr>
        <tr><td class="center">2</td><td>Disiplin</td><td class="center">{{ toKategori($penilaian->kedisiplinan) }}</td></tr>
        <tr><td class="center">3</td><td>Kerja Sama</td><td class="center">{{ toKategori($penilaian->kerja_sama) }}</td></tr>
        <tr><td class="center">4</td><td>Tanggung Jawab</td><td class="center">{{ toKategori($penilaian->tanggung_jawab) }}</td></tr>
        <tr><td class="center">5</td><td>Komunikasi</td><td class="center">{{ toKategori($penilaian->komunikasi) }}</td></tr>
        <tr><td class="center">6</td><td>Produktivitas</td><td class="center">{{ toKategori($penilaian->produktivitas) }}</td></tr>
            {{-- <tr class="bold"><td colspan="2" class="center">Nilai Total</td><td class="center">{{ $penilaian->nilai_total }}</td></tr>
            <tr class="bold"><td colspan="2" class="center">Kategori</td><td class="center uppercase">{{ $penilaian->kategori }}</td></tr> --}}
        </tbody>
    </table>
    @endif

{{-- PENILAIAN AKHIR --}}
    <table>
        <tr>
          <td class="bold bg-gray mt-3" style="width: 200px;">NILAI SKP</td>
            <td>{{ $skp->predikat ?? '-' }}</td> 
        </tr>
        <tr>
          <td class="bold bg-gray">KINERJA PEGAWAI</td>
            <td>{{ $penilaian->kategori ?? '-' }}</td> 
        </tr>
    </table>


    {{-- TANDA TANGAN --}}
    <table class="no-border center" style="margin-top: 45px;">
        <tr class="no-border">
            <td class="no-border">
                Cianjur, {{ now()->translatedFormat('d F Y') }}<br>
                <b>Pegawai</b><br><br><br><br>
                <b class="uppercase">{{ $skp->pegawai->user->name }}</b><br>
                NIP. {{ $skp->pegawai->nip ?? '-' }}
            </td>
            <td class="no-border">
                Cianjur, {{ now()->translatedFormat('d F Y') }}<br>
                <b>Penilai Kinerja</b><br><br><br><br>
                <b class="uppercase">{{ $atasan->user->name ?? '-' }}</b><br>
                NIP. {{ $atasan->nip ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>
