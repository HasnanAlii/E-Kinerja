<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i data-feather="file-text" class="w-5 h-5 text-indigo-600"></i>
                Detail SKP Bawahan
            </h2>
  
        </div>
    </x-slot>
    

    <div class="py-8 bg-gray-100 min-h-screen print:bg-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ACTION BAR (TOMBOL AKSI) --}}
            <div class="my-10 pt-6 border-t border-gray-200 print:hidden">
                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-4">
                    
                    {{-- KIRI: TOMBOL KEMBALI --}}
                    <a href="{{ route('atasan.skp.index') }}" 
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                        Kembali ke Daftar
                    </a>

                    {{-- KANAN: AKSI UTAMA --}}
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        
                        {{-- 1. Tombol Edit Umpan Balik --}}
                        <a href="{{ route('skp.feedback.edit', $skp->id) }}" 
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-xl hover:bg-indigo-100 hover:border-indigo-200 transition-all duration-200 shadow-sm">
                            <i data-feather="edit-3" class="w-4 h-4 mr-2"></i>
                            Edit / Isi Umpan Balik
                        </a>

                        {{-- 2. Tombol Beri Penilaian (Muncul jika status Diajukan) --}}
                        @if($skp->status == 'Diajukan')
                            <button onclick="openModal('modalPenilaian')" 
                                    class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white bg-green-600 rounded-xl shadow-md hover:bg-green-700 hover:shadow-lg transition-all duration-200 transform active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i data-feather="check-square" class="w-4 h-4 mr-2"></i>
                                Beri Penilaian & Validasi
                            </button>
                        @endif

                    </div>
                </div>
            </div>
            {{-- CONTAINER DOKUMEN (A4 Style) --}}
            <div class="bg-white shadow-xl sm:rounded-none border border-gray-300 p-8 sm:p-12 print:shadow-none print:border-none print:p-0">

                {{-- 1. HEADER DOKUMEN --}}
                <div class="text-center mb-6 uppercase font-bold text-gray-900 leading-relaxed">
                    <h1 class="text-lg">EVALUASI KINERJA PEGAWAI</h1>
                    <h2 class="text-base">PENDEKATAN HASIL KERJA KUANTITATIF</h2>
                    <p class="mt-2 text-sm">PERIODE: {{ $skp->periode }}</p>
                </div>

                {{-- INFO PEMERINTAH & PERIODE PENILAIAN --}}
                <div class="flex justify-between text-sm font-bold text-gray-800 mb-2 uppercase border-b-2 border-black pb-1">
                    <div>PEMERINTAH KAB. CIANJUR</div>
                    <div>
                        PERIODE PENILAIAN: 
                        {{ $skp->tanggal_mulai ? \Carbon\Carbon::parse($skp->tanggal_mulai)->format('d M Y') : '-' }} 
                        SD 
                        {{ $skp->tanggal_selesai ? \Carbon\Carbon::parse($skp->tanggal_selesai)->format('d M Y') : '-' }}
                    </div>
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
                                <td class="border-b border-gray-300 p-1 font-semibold uppercase">{{ $skp->pegawai->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="border-b border-gray-300 p-1 text-center align-top">2</td>
                                <td class="border-b border-gray-300 p-1">NIP</td>
                                <td class="border-b border-gray-300 p-1">{{ $skp->pegawai->nip ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="border-b border-gray-300 p-1 text-center align-top">3</td>
                                <td class="border-b border-gray-300 p-1">PANGKAT/GOL</td>
                                <td class="border-b border-gray-300 p-1">{{ $skp->pegawai->golongan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="border-b border-gray-300 p-1 text-center align-top">4</td>
                                <td class="border-b border-gray-300 p-1">JABATAN</td>
                                <td class="border-b border-gray-300 p-1">{{ $skp->pegawai->jabatan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1 text-center align-top">5</td>
                                <td class="p-1">UNIT KERJA</td>
                                <td class="p-1">{{ $skp->pegawai->bidang->nama_bidang ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- KOLOM KANAN: PEJABAT PENILAI --}}
                    <div>
                        <table class="w-full">
                            <tr>
                                <th class="border-b border-black p-1 w-8 text-center">NO</th>
                                <th class="border-b border-black p-1 text-left" colspan="2">PEJABAT PENILAI KINERJA</th>
                            </tr>
                            @php $atasan = $skp->pegawai->atasan; @endphp
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
                                <td class="p-1">{{ $atasan->bidang->nama_bidang  ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- 3. CAPAIAN ORGANISASI & POLA DISTRIBUSI --}}
                <div class="border-x border-b border-black mb-1 text-sm">
                    <div class="p-2 border-b border-black">
                        <span class="font-bold">CAPAIAN KINERJA ORGANISASI:</span>
                        <span class="ml-2">{{ $skp->capaian_kinerja_organisasi ?? '-' }}</span>
                    </div>
                    <div class="p-2">
                        <span class="font-bold">POLA DISTRIBUSI:</span>
                        <span class="ml-2">{{ $skp->pola_distribusi ?? '-' }}</span>
                    </div>
                </div>

                {{-- 4. TABEL HASIL KERJA --}}
                <div class="overflow-x-auto mb-6">
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
                                // Filter hanya yang jenis 'Utama' lalu grup berdasarkan RHK
                                $utama = $skp->hasilKerja->where('jenis', 'Utama');
                                $groupedUtama = $utama->groupBy('rhk'); 
                                $no = 1;
                            @endphp

                            @forelse($groupedUtama as $rhk => $group)
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
                            
                            @php
                                // Filter jenis 'Hasil Kerja' (Tambahan)
                                $tambahan = $skp->hasilKerja->where('jenis', 'Hasil Kerja');
                                $groupedTambahan = $tambahan->groupBy('rhk');
                                $noTambahan = 1;
                            @endphp

                            @forelse($groupedTambahan as $rhk => $group)
                                @php 
                                    $rowspan = $group->count(); 
                                    $first = $group->first();
                                @endphp
                                <tr>
                                    <td class="border border-black p-2 text-center align-top" rowspan="{{ $rowspan }}">{{ $noTambahan++ }}</td>
                                    <td class="border border-black p-2 align-top" rowspan="{{ $rowspan }}">{{ $first->rhk_pimpinan }}</td>
                                    <td class="border border-black p-2 align-top" rowspan="{{ $rowspan }}">{{ $first->rhk }}</td>
                                    <td class="border border-black p-2 text-center align-top">{{ $first->aspek }}</td>
                                    <td class="border border-black p-2 align-top">{{ $first->indikator_kinerja }}</td>
                                    <td class="border border-black p-2 text-center align-top">{{ $first->target }}</td>
                                    <td class="border border-black p-2 align-top">{{ $first->realisasi ?? '-' }}</td>
                                    <td class="border border-black p-2 align-top">{{ $first->umpan_balik ?? '-' }}</td>
                                </tr>
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
                                    <td colspan="8" class="border border-black p-4 text-center text-gray-500 italic">-</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                {{-- 5. TABEL PERILAKU KERJA --}}
                {{-- <div class="overflow-x-auto">
                    <h3 class="font-bold text-sm mb-1 border border-black border-b-0 p-1 bg-gray-50">PERILAKU KERJA</h3>
                    <table class="w-full border-collapse border border-black text-xs sm:text-sm">
                        <thead class="bg-gray-100 font-bold text-center">
                            <tr>
                                <th class="border border-black p-2 w-8">NO</th>
                                <th class="border border-black p-2 w-32">ASPEK PERILAKU</th>
                                <th class="border border-black p-2">INDIKATOR PERILAKU & EKSPEKTASI KHUSUS</th>
                                <th class="border border-black p-2 w-40">UMPAN BALIK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($skp->perilaku as $index => $p)
                                <tr>
                                    <td class="border border-black p-2 text-center align-top">{{ $loop->iteration }}</td>
                                    <td class="border border-black p-2 font-semibold align-top">{{ $p->aspek }}</td>
                                    <td class="border border-black p-2 align-top">
                                        <div class="mb-1 text-gray-900 font-medium">{{ $p->perilaku }}</div>
                                        <div class="text-gray-600 italic">Ekspektasi: {{ $p->ekspektasi ?? '-' }}</div>
                                    </td>
                                    <td class="border border-black p-2 align-top">{{ $p->umpan_balik ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="border border-black p-4 text-center text-gray-500 italic">Data perilaku belum diisi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div> --}}

                <div class="mt-4 border border-black p-4 flex justify-between items-center bg-gray-50">
                    <div class="text-sm font-bold">RATING HASIL KERJA: <span class="text-indigo-700">{{ $skp->rating ?? '-' }}</span></div>
                    <div class="text-sm font-bold">PREDIKAT KINERJA: <span class="text-indigo-700 uppercase">{{ $skp->predikat ?? '-' }}</span></div>
                </div>

                <div class="mt-10 flex justify-between px-10 print:mt-16">
                    <div class="text-center w-1/2">
                        <p class="mb-16">
                            Cianjur, {{ now()->translatedFormat('d F Y') }} <br>
                            <span class="font-semibold">Pegawai Yang Dinilai</span>
                        </p>

                        <p class="font-bold border-b border-black inline-block min-w-[200px] py-1">
                            {{ $skp->pegawai->user->name ?? '(Nama Pegawai)' }}
                        </p>
                        <p class="mt-1">NIP. {{ $skp->pegawai->nip ?? '.......................' }}</p>
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

           

        </div>
    </div>


    {{-- MODAL PENILAIAN --}}
    <div id="modalPenilaian" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        {{-- Backdrop Gelap --}}
        <div class="fixed inset-0 bg-gray-900/75 transition-opacity" onclick="closeModal('modalPenilaian')"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                {{-- Header Modal --}}
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i data-feather="award" class="h-6 w-6 text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Penilaian Kinerja</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Berikan rating hasil kerja dan predikat kinerja untuk pegawai <strong>{{ $skp->pegawai->user->name }}</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Penilaian --}}
                <form action="{{ route('atasan.skp.nilai', $skp->id) }}" method="POST">
                    @csrf
                    <div class="px-6 py-6 space-y-5">
                                        
                    {{-- Input 1: Rating Hasil Kerja --}}
                    <div>
                        <label for="rating" class="block text-sm font-bold text-gray-700 mb-1">
                            Rating Hasil Kerja
                        </label>
                        <p class="text-xs text-gray-500 mb-2">
                            Evaluasi pencapaian target output pegawai.
                        </p>
                        <div class="relative">
                            <select name="rating" id="rating" required 
                                    class="w-full appearance-none rounded-xl border-gray-300 py-2.5 pl-4 pr-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="" disabled selected>-- Pilih Rating --</option>
                                <option value="Di Atas Ekspektasi">Di Atas Ekspektasi</option>
                                <option value="Sesuai Ekspektasi">Sesuai Ekspektasi</option>
                                <option value="Di Bawah Ekspektasi">Di Bawah Ekspektasi</option>
                            </select>
                        
                    </div>

                <div>
                    <label for="predikat" class="block text-sm font-bold text-gray-700 mb-1">
                        Predikat Kinerja Akhir
                    </label>
                    <p class="text-xs text-gray-500 mb-2">
                        Kesimpulan akhir gabungan hasil kerja & perilaku.
                    </p>
                    <div class="relative">
                        <select name="predikat" id="predikat" required 
                                class="w-full appearance-none rounded-xl border-gray-300 py-2.5 pl-4 pr-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="" disabled selected>-- Pilih Predikat --</option>
                            <option value="Sangat Baik" class="font-semibold text-green-600">Sangat Baik</option>
                            <option value="Baik" class="font-semibold text-blue-600">Baik</option>
                            <option value="Cukup" class="font-semibold text-yellow-600">Cukup / Butuh Perbaikan</option>
                            <option value="Kurang" class="font-semibold text-orange-600">Kurang</option>
                            <option value="Sangat Kurang" class="font-semibold text-red-600">Sangat Kurang</option>
                        </select>
                    
                    </div>
                </div>

                {{-- Info Tambahan --}}
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded-r-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-feather="alert-circle" class="h-4 w-4 text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-yellow-700">
                                <strong>Perhatian:</strong> Setelah disimpan, status SKP akan berubah menjadi <strong>Selesai</strong> dan tidak dapat diubah lagi.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

                    {{-- Footer Modal --}}
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto transition">
                            Simpan & Validasi
                        </button>
                        <button type="button" onclick="closeModal('modalPenilaian')" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">
                            Batal
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        // Inisialisasi Feather Icons
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        // Fungsi Buka Modal
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // Mencegah scroll background
        }

        // Fungsi Tutup Modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    </script>
</x-app-layout>