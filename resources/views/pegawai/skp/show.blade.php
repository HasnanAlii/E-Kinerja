<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i data-feather="file-text" class="w-5 h-5 text-indigo-600"></i>
                Detail Evaluasi Kinerja
            </h2>
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 print:hidden">
                <i data-feather="printer" class="w-4 h-4 inline mr-1"></i> Cetak
            </button>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen print:bg-white">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    
        <div class="mt-6 flex items-center mb-2  justify-between">

            {{-- Tombol Kembali --}}
            <a href="{{ route('skp.index') }}" 
            class="inline-flex items-center text-base text-indigo-600 hover:text-indigo-800 transition">
                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali ke Daftar
            </a>

            {{-- Tombol Tambah RHK --}}
            <a href="{{ route('skp.hasil_kerja.create', $data->id) }}"
            class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm 
                    hover:bg-indigo-700 transition duration-150 ease-in-out text-base">
                <i data-feather="plus" class="w-5 h-5 mr-2"></i>
                Tambah RHK
            </a>

        </div>

            {{-- CONTAINER DOKUMEN (A4 Style) --}}
            <div class="bg-white shadow-xl sm:rounded-none border border-gray-300 p-8 sm:p-12 print:shadow-none print:border-none print:p-0">
      
                {{-- 1. HEADER DOKUMEN --}}
                <div class="text-center mb-6 uppercase font-bold text-gray-900 leading-relaxed">
                    <h1 class="text-lg">EVALUASI KINERJA PEGAWAI</h1>
                    <h2 class="text-base">PENDEKATAN HASIL KERJA KUANTITATIF</h2>
                    <p class="mt-2 text-sm">PERIODE: {{ $data->periode }}</p>
                </div>

                {{-- INFO PEMERINTAH & PERIODE PENILAIAN --}}
                <div class="flex justify-between text-sm font-bold text-gray-800 mb-2 uppercase border-b-2 border-black pb-1">
                    <div>PEMERINTAH KAB. CIANJUR</div>
                    <div>PERIODE PENILAIAN: {{ \Carbon\Carbon::parse($data->tanggal_mulai)->format('d M Y') }} SD {{ \Carbon\Carbon::parse($data->tanggal_selesai)->format('d M Y') }}</div>
                </div>

                {{-- 2. TABEL IDENTITAS (Grid 2 Kolom) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 border border-black mb-1 text-sm">
                    
                    {{-- KOLOM KIRI: PEGAWAI YANG DINILAI --}}
                    <div class="border-r border-black">
                        <table class="w-full">
                            <tr>
                                <th class="border-b border-black p-1 w-8 text-center">NO</th>
                                <th class="border-b border-black p-1 text-left" colspan="2">PEGAWAI YANG DINILAI</th>
                            </tr>
                            <tr>
                                <td class="border-b border-gray-300 p-1 text-center align-top">1</td>
                                <td class="border-b border-gray-300 p-1 w-32">NAMA</td>
                                <td class="border-b border-gray-300 p-1 font-semibold uppercase">{{ $data->pegawai->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="border-b border-gray-300 p-1 text-center align-top">2</td>
                                <td class="border-b border-gray-300 p-1">NIP</td>
                                <td class="border-b border-gray-300 p-1">{{ $data->pegawai->nip ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="border-b border-gray-300 p-1 text-center align-top">3</td>
                                <td class="border-b border-gray-300 p-1">PANGKAT/GOL</td>
                                <td class="border-b border-gray-300 p-1">{{ $data->pegawai->golongan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="border-b border-gray-300 p-1 text-center align-top">4</td>
                                <td class="border-b border-gray-300 p-1">JABATAN</td>
                                <td class="border-b border-gray-300 p-1">{{ $data->pegawai->jabatan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1 text-center align-top">5</td>
                                <td class="p-1">UNIT KERJA</td>
                                <td class="p-1">{{ $data->pegawai->bidang->nama_bidang ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- KOLOM KANAN: PEJABAT PENILAI --}}
                    {{-- Asumsi: Relasi ke atasan ada via $data->pegawai->atasan --}}
                    <div>
                        <table class="w-full">
                            <tr>
                                <th class="border-b border-black p-1 w-8 text-center">NO</th>
                                <th class="border-b border-black p-1 text-left" colspan="2">ATASAN PENILAI KINERJA</th>
                            </tr>
                            @php $atasan = $data->pegawai->atasan; @endphp
                            <tr>
                                <td class="border-b border-gray-300 p-1 text-center align-top">1</td>
                                <td class="border-b border-gray-300 p-1 w-32">NAMA</td>
                                <td class="border-b border-gray-300 p-1 font-semibold uppercase">{{ $atasan->user->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="border-b border-gray-300 p-1 text-center align-top">2</td>
                                <td class="border-b border-gray-300 p-1">NIP</td>
                                <td class="border-b border-gray-300 p-1">{{ $atasan->nip ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="border-b border-gray-300 p-1 text-center align-top">3</td>
                                <td class="border-b border-gray-300 p-1">PANGKAT/GOL</td>
                                <td class="border-b border-gray-300 p-1">{{ $atasan->golongan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="border-b border-gray-300 p-1 text-center align-top">4</td>
                                <td class="border-b border-gray-300 p-1">JABATAN</td>
                                <td class="border-b border-gray-300 p-1">{{ $atasan->jabatan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1 text-center align-top">5</td>
                                <td class="p-1">UNIT KERJA</td>
                                <td class="p-1">{{ $atasan->bidang->nama_bidang ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- 3. CAPAIAN ORGANISASI & POLA DISTRIBUSI --}}
                <div class="border-x border-b border-black mb-1 text-sm">
                    <div class="p-2 border-b border-black">
                        <span class="font-bold">CAPAIAN KINERJA ORGANISASI:</span>
                        <span class="ml-2">{{ $data->capaian_kinerja_organisasi ?? '-' }}</span>
                    </div>
                    <div class="p-2">
                        <span class="font-bold">POLA DISTRIBUSI:</span>
                        <span class="ml-2">{{ $data->pola_distribusi ?? '-' }}</span>
                    </div>
                </div>

                {{-- 4. TABEL HASIL KERJA --}}
                <div class="overflow-x-auto">
                    <h3 class="font-bold text-sm mb-1 border border-black border-b-0 p-1 bg-gray-50">HASIL KERJA</h3>
                    
                    <table class="w-full border-collapse border border-black text-xs sm:text-sm">
                        <thead class="text-center font-bold bg-gray-50">
                            <tr>
                                <th class="border border-black p-2 w-8">NO</th>
                                <th class="border border-black p-2 w-1/5">RENCANA HASIL KERJA PIMPINAN YANG DIINTERVENSI</th>
                                <th class="border border-black p-2 w-1/5">RENCANA HASIL KERJA</th>
                                <th class="border border-black p-2 w-24">ASPEK</th>
                                <th class="border border-black p-2">INDIKATOR KINERJA INDIVIDU</th>
                                <th class="border border-black p-2 w-24">TARGET / EKSPEKTASI</th>
                                <th class="border border-black p-2 w-40">REALISASI BERDASARKAN BUKTI DUKUNG</th>
                                <th class="border border-black p-2 w-32">UMPAN BALIK BERKELANJUTAN</th>
                            </tr>
                            <tr class="text-[10px] text-gray-600">
                                <th class="border border-black p-1">(1)</th>
                                <th class="border border-black p-1">(2)</th>
                                <th class="border border-black p-1">(3)</th>
                                <th class="border border-black p-1">(4)</th>
                                <th class="border border-black p-1">(5)</th>
                                <th class="border border-black p-1">(6)</th>
                                <th class="border border-black p-1">(7)</th>
                                <th class="border border-black p-1">(8)</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            {{-- SECTION UTAMA --}}
                            <tr class="bg-gray-100 font-bold">
                                <td colspan="8" class="border border-black p-1 text-left pl-2">UTAMA</td>
                            </tr>

                            @php
                                // Kelompokkan berdasarkan RHK untuk rowspan
                                $groupedHasilKerja = $data->hasilKerja->groupBy('rhk'); 
                                $no = 1;
                            @endphp

                            @forelse($groupedHasilKerja as $rhk => $group)
                                @php 
                                    $rowspan = $group->count(); 
                                    $first = $group->first();
                                @endphp

                                <tr>
                                    {{-- Kolom 1-3 (Merged) --}}
                                    <td class="border border-black p-2 text-center align-top" rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                                    <td class="border border-black p-2 align-top" rowspan="{{ $rowspan }}">{{ $first->rhk_pimpinan }}</td>
                                    <td class="border border-black p-2 align-top" rowspan="{{ $rowspan }}">{{ $first->rhk }}</td>

                                    {{-- Baris Pertama Data Aspek --}}
                                    <td class="border border-black p-2 text-center align-top">{{ $first->aspek }}</td>
                                    <td class="border border-black p-2 align-top">{{ $first->indikator_kinerja }}</td>
                                    <td class="border border-black p-2 text-center align-top">{{ $first->target }}</td>
                                    <td class="border border-black p-2 align-top">{{ $first->realisasi ?? '-' }}</td>
                                    <td class="border border-black p-2 align-top">{{ $first->umpan_balik ?? '-' }}</td>
                                </tr>

                                {{-- Baris Sisa Data Aspek (Kualitas/Waktu) --}}
                                @foreach($group->skip(1) as $item)
                                    <tr>
                                        <td class="border border-black p-2 text-center align-top">{{ $item->aspek }}</td>
                                        <td class="border border-black p-2 align-top">{{ $item->indikator_kinerja }}</td>
                                        <td class="border border-black p-2 text-center align-top">{{ $item->target }}</td>
                                        <td class="border border-black p-2 align-top">{{ $item->realisasi ?? '-' }}</td>
                                        <td class="border border-black p-2 align-top">{{ $item->umpan_balik ?? '-' }}</td>
                                    </tr>
                                @endforeach

                            @empty
                                <tr>
                                    <td colspan="8" class="border border-black p-4 text-center text-gray-500 italic">Data hasil kerja utama belum tersedia.</td>
                                </tr>
                            @endforelse

                            {{-- SECTION TAMBAHAN (Jika ada) --}}
                            <tr class="bg-gray-100 font-bold">
                                <td colspan="8" class="border border-black p-1 text-left pl-2">TAMBAHAN</td>
                            </tr>
                            {{-- (Looping serupa bisa ditambahkan di sini untuk Tugas Tambahan jika ada datanya) --}}
                            <tr>
                                <td colspan="8" class="border border-black p-4 text-center text-gray-500 italic">-</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                {{-- 5. FOOTER / TANDA TANGAN (Opsional untuk layout laporan) --}}
           
                <div class="mt-10 flex justify-between px-10 print:mt-16">
                    <div class="text-center w-1/2">
                        <p class="mb-16">
                            Cianjur, {{ now()->translatedFormat('d F Y') }} <br>
                            <span class="font-semibold">Pegawai Yang Dinilai</span>
                        </p>

                        <p class="font-bold border-b border-black inline-block min-w-[200px] py-1">
                            {{ $data->pegawai->user->name ?? '(Nama Pegawai)' }}
                        </p>
                        <p class="mt-1">NIP. {{ $data->pegawai->nip ?? '.......................' }}</p>
                    </div>

                    {{-- ATASAN --}}
                    <div class="text-center w-1/2">
                        <p class="mb-16">
                            Cianjur, {{ now()->translatedFormat('d F Y') }} <br>
                            <span class="font-semibold">Atasan Langsung</span>
                        </p>

                        <p class="font-bold border-b border-black inline-block min-w-[200px] py-1">
                            {{ $atasan->user->name ?? '(Nama Pejabat Penilai)' }}
                        </p>
                        <p class="mt-1">NIP. {{ $atasan->nip ?? '.......................' }}</p>
                    </div>

                </div>

            </div>
            {{-- END CONTAINER --}}

      

        </div>
    </div>

    {{-- CSS Khusus Cetak --}}
    <style>
        @media print {
            body { background-color: white; }
            header, footer, .print\:hidden { display: none !important; }
            .shadow-xl { box-shadow: none !important; }
            .border-gray-300 { border-color: black !important; }
        }
    </style>
</x-app-layout>