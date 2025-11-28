<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i data-feather="edit-3" class="w-5 h-5 text-indigo-600"></i>
                Edit Evaluasi Kinerja Pegawai
            </h2>

            <a href="{{ route('skp.show', $skp->id) }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-1"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="bg-gray-100 min-h-screen py-8">
        <div class=" mx-auto sm:px-6 lg:px-8">

            <form action="{{ route('skp.update', $skp->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white shadow-xl border border-gray-300 p-8 sm:p-12">

                    {{-- HEADER DOKUMEN --}}
                    <div class="text-center mb-6 uppercase font-bold text-gray-900 leading-relaxed">
                        <h1 class="text-lg">EVALUASI KINERJA PEGAWAI</h1>
                        <h2 class="text-base">PENDEKATAN HASIL KERJA KUANTITATIF</h2>
                        <p class="mt-2 text-sm">PERIODE: {{ $skp->periode }}</p>
                    </div>

                    {{-- INFO PEMERINTAH --}}
                    <div class="flex justify-between text-sm font-bold text-gray-800 mb-2 uppercase border-b-2 border-black pb-1">
                        <div>PEMERINTAH KAB. CIANJUR</div>
                        <div>PERIODE PENILAIAN: 
                            {{ \Carbon\Carbon::parse($skp->tanggal_mulai)->format('d M Y') }} SD 
                            {{ \Carbon\Carbon::parse($skp->tanggal_selesai)->format('d M Y') }}
                        </div>
                    </div>

                    {{-- TABEL IDENTITAS --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 border border-black mb-1 text-sm">

                        {{-- KOLOM KIRI PEGAWAI --}}
                        <div class="border-r border-black">
                            <table class="w-full">
                                <tr>
                                    <th class="border-b border-black p-1 w-8 text-center">NO</th>
                                    <th class="border-b border-black p-1 text-left" colspan="2">PEGAWAI YANG DINILAI</th>
                                </tr>
                                <tr>
                                    <td class="border-b border-gray-300 p-1 text-center">1</td>
                                    <td class="border-b border-gray-300 p-1 w-32">NAMA</td>
                                    <td class="border-b border-gray-300 p-1 font-semibold uppercase">
                                        {{ $skp->pegawai->user->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border-b border-gray-300 p-1 text-center">2</td>
                                    <td class="border-b border-gray-300 p-1">NIP</td>
                                    <td class="border-b border-gray-300 p-1">{{ $skp->pegawai->nip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-b border-gray-300 p-1 text-center">3</td>
                                    <td class="border-b border-gray-300 p-1">PANGKAT/GOL</td>
                                    <td class="border-b border-gray-300 p-1">{{ $skp->pegawai->golongan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-b border-gray-300 p-1 text-center">4</td>
                                    <td class="border-b border-gray-300 p-1">JABATAN</td>
                                    <td class="border-b border-gray-300 p-1">{{ $skp->pegawai->jabatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="p-1 text-center">5</td>
                                    <td class="p-1">UNIT KERJA</td>
                                    <td class="p-1">{{ $skp->pegawai->bidang->nama_bidang ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- KOLOM KANAN ATASAN --}}
                        <div>
                            <table class="w-full">
                                <tr>
                                    <th class="border-b border-black p-1 w-8 text-center">NO</th>
                                    <th class="border-b border-black p-1 text-left" colspan="2">ATASAN PENILAI KINERJA</th>
                                </tr>

                                @php $atasan = $skp->pegawai->atasan; @endphp

                                <tr>
                                    <td class="border-b border-gray-300 p-1 text-center">1</td>
                                    <td class="border-b border-gray-300 p-1 w-32">NAMA</td>
                                    <td class="border-b border-gray-300 p-1 font-semibold uppercase">
                                        {{ $atasan->user->name ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border-b border-gray-300 p-1 text-center">2</td>
                                    <td class="border-b border-gray-300 p-1">NIP</td>
                                    <td class="border-b border-gray-300 p-1">{{ $atasan->nip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-b border-gray-300 p-1 text-center">3</td>
                                    <td class="border-b border-gray-300 p-1">PANGKAT/GOL</td>
                                    <td class="border-b border-gray-300 p-1">{{ $atasan->golongan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-b border-gray-300 p-1 text-center">4</td>
                                    <td class="border-b border-gray-300 p-1">JABATAN</td>
                                    <td class="border-b border-gray-300 p-1">{{ $atasan->jabatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="p-1 text-center">5</td>
                                    <td class="p-1">UNIT KERJA</td>
                                    <td class="p-1">{{ $atasan->bidang->nama_bidang ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                    </div>

                    {{-- CAPAIAN KINERJA --}}
                    <div class="border-x border-b border-black mb-1 text-sm">
                        <div class="p-2 border-b border-black font-bold">
                            CAPAIAN KINERJA ORGANISASI:
                            <span class="font-normal ml-2">{{ $skp->capaian_kinerja_organisasi ?? '-' }}</span>
                        </div>
                        <div class="p-2 font-bold">
                            POLA DISTRIBUSI:
                            <span class="font-normal ml-2">{{ $skp->pola_distribusi ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- HASIL KERJA --}}
                    <h3 class="font-bold text-sm mb-1 border border-black border-b-0 p-1 bg-gray-50">HASIL KERJA</h3>

                    <table class="w-full border-collapse border border-black text-xs sm:text-sm">

                        {{-- HEADER --}}
                        <thead class="text-center font-bold bg-gray-50">
                            <tr>
                                <th class="border border-black p-2 w-8">NO</th>
                                <th class="border border-black p-2 w-1/5">RENCANA HASIL KERJA PIMPINAN YANG DIINTERVENSI</th>
                                <th class="border border-black p-2 w-1/5">RENCANA HASIL KERJA</th>
                                <th class="border border-black p-2 w-32">ASPEK</th>
                                <th class="border border-black p-2 ">INDIKATOR KINERJA INDIVIDU</th>
                                <th class="border border-black p-2 w-32">TARGET</th>
                                <th class="border border-black p-2 ">REALISASI</th>
                                <th class="border border-black p-2 w-28">UMPAN BALIK</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr class="bg-gray-100 font-bold">
                                <td colspan="8" class="border border-black p-1 text-left pl-2">UTAMA</td>
                            </tr>

                            @foreach($skp->hasilKerja as $i => $row)
                            <tr>
                                <td class="border border-black p-2 text-center align-top">{{ $i + 1 }}</td>

                                {{-- RHK PIMPINAN --}}
                                <td class="border border-black p-2 align-top">
                                    <textarea readonly class="w-full bg-gray-100 border-none" rows="6">{{ $row->rhk_pimpinan }}</textarea>
                                </td>

                                {{-- RHK --}}
                                <td class="border border-black p-2 align-top">
                                    <textarea name="hasil_kerja[{{ $row->id }}][rhk]" class="w-full" rows="6">{{ $row->rhk }}</textarea>
                                </td>

                                {{-- ASPEK --}}
                                <td class="border border-black p-2 align-top text-center">
                                    <textarea name="hasil_kerja[{{ $row->id }}][aspek]" class="w-full text-center" rows="6">{{ $row->aspek }}</textarea>
                                </td>

                                {{-- INDIKATOR --}}
                                <td class="border border-black p-2 align-top">
                                    <textarea name="hasil_kerja[{{ $row->id }}][indikator_kinerja]" class="w-full" rows="6">{{ $row->indikator_kinerja }}</textarea>
                                </td>

                                {{-- TARGET --}}
                                <td class="border border-black p-2 align-top text-center">
                                    <textarea name="hasil_kerja[{{ $row->id }}][target]" class="w-full text-left" rows="6">{{ $row->target }}</textarea>
                                </td>

                                {{-- REALISASI --}}
                                <td class="border border-black p-2 align-top text-left">
                                    <textarea name="hasil_kerja[{{ $row->id }}][realisasi]" class="w-full text-left" rows="6">{{ $row->realisasi }}</textarea>
                                </td>

                                {{-- UMPAN BALIK --}}
                                <td class="border border-black p-2 align-top">
                                    <textarea readonly class="w-full bg-gray-100 border-none" rows="6">{{ $row->umpan_balik }}</textarea>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>

                    {{-- BUTTON SECTION --}}
                    <div class="flex justify-end gap-4 pt-6 mt-6 border-t">
                        <a href="{{ route('skp.show', $skp->id) }}"
                            class="px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </a>

                        <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>feather.replace();</script>
</x-app-layout>
