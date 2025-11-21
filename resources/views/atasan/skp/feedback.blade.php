<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i data-feather="edit-3" class="w-6 h-6 text-indigo-600"></i>
                Isi Umpan Balik & Evaluasi SKP
            </h2>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- FORM --}}
            <form action="{{ route('skp.feedback.update', $skp->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- CARD UTAMA --}}
                <div class="bg-white shadow-xl rounded-xl border border-gray-200 p-8">

                    {{-- Header Info Pegawai --}}
                    <div class="mb-6 pb-4 border-b border-gray-200 flex items-center gap-3">
                        <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                            <i data-feather="user-check" class="w-6 h-6"></i>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Pegawai: {{ $skp->pegawai->user->name }}</h3>
                            <p class="text-sm text-gray-500">Silakan isi <strong>Umpan Balik</strong> pada masing-masing item di bawah.</p>
                        </div>
                    </div>

                    {{-- TITLE SECTION --}}
                    <div class="bg-gray-50 p-3 mb-3 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-700 text-sm flex items-center gap-2">
                            <i data-feather="clipboard" class="w-4 h-4 text-indigo-600"></i>  
                            Hasil Kerja Pegawai
                        </h3>
                    </div>

                    {{-- TABLE --}}
                    <div class="overflow-x-auto mb-10">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-100 text-left text-gray-700 font-semibold border-b border-gray-300">
                                    <th class="p-3 w-10 text-center">No</th>
                                    <th class="p-3 w-1/5">RHK Intervensi Pimpinan</th>
                                    <th class="p-3 w-1/5">RHK</th>
                                    <th class="p-3 w-24 text-center">Aspek</th>
                                    <th class="p-3">Indikator</th>
                                    <th class="p-3 w-32 text-center">Target</th>
                                    <th class="p-3 w-32 text-center">Realisasi</th>
                                    <th class="p-3 w-64 text-center bg-yellow-50">Umpan Balik</th>
                                </tr>
                            </thead>

                            <tbody>

                                {{-- LABEL UTAMA --}}
                                <tr>
                                    <td colspan="8" class="p-2 bg-gray-50 text-xs font-bold text-gray-600 tracking-wider border-y">
                                        UTAMA
                                    </td>
                                </tr>

                                @php
                                    $grouped = $skp->hasilKerja->where('jenis', 'Utama')->groupBy('rhk');
                                    $no = 1;
                                @endphp

                                @foreach($grouped as $rhk => $group)
                                    @php 
                                        $rowspan = $group->count(); 
                                        $first = $group->first(); 
                                    @endphp

                                    {{-- ROW UTAMA --}}
                                    <tr class="border-b">
                                        {{-- NO --}}
                                        <td class="p-3 text-center align-top" rowspan="{{ $rowspan }}">
                                            {{ $no++ }}
                                        </td>

                                        {{-- RHK PIMPINAN (EDITABLE) --}}
                                        <td class="p-3 bg-yellow-50 align-top" rowspan="{{ $rowspan }}">
                                            <textarea name="rhk_pimpinan[{{ $first->id }}]"
                                                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                rows="8">{{ $first->rhk_pimpinan }}</textarea>
                                        </td>

                                        {{-- RHK --}}
                                        <td class="p-3 align-top font-medium text-gray-800" rowspan="{{ $rowspan }}">
                                            {{ $first->rhk }}
                                        </td>

                                        {{-- ASPEK --}}
                                        <td class="p-3 text-center align-top">
                                            {{ $first->aspek }}
                                        </td>

                                        {{-- INDIKATOR --}}
                                        <td class="p-3 align-top">
                                            {{ $first->indikator_kinerja }}
                                        </td>

                                        {{-- TARGET --}}
                                        <td class="p-3 text-center align-top">
                                            {{ $first->target }}
                                        </td>

                                        {{-- REALISASI --}}
                                        <td class="p-3 text-center align-top">
                                            {{ $first->realisasi ?? '-' }}
                                        </td>

                                        {{-- UMPAN BALIK --}}
                                        <td class="p-3 bg-yellow-50 align-top">
                                            <textarea name="hasil_kerja[{{ $first->id }}]"
                                                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                rows="3" placeholder="Berikan umpan balik...">{{ $first->umpan_balik }}</textarea>
                                        </td>
                                    </tr>

                                    {{-- ROW LANJUTAN --}}
                                    @foreach($group->skip(1) as $item)
                                        <tr class="border-b">
                                            <td class="p-3 text-center align-top">{{ $item->aspek }}</td>
                                            <td class="p-3 align-top">{{ $item->indikator_kinerja }}</td>
                                            <td class="p-3 text-center align-top">{{ $item->target }}</td>
                                            <td class="p-3 text-center align-top">{{ $item->realisasi ?? '-' }}</td>
                                            <td class="p-3 bg-yellow-50 align-top">
                                                <textarea name="hasil_kerja[{{ $item->id }}]"
                                                    class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                    rows="3" placeholder="Berikan umpan balik...">{{ $item->umpan_balik }}</textarea>
                                            </td>
                                        </tr>
                                    @endforeach

                                @endforeach

                            </tbody>
                        </table>
                    </div>


                    {{-- BUTTON AREA --}}
                    <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('atasan.skp.show', $skp->id) }}"
                           class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                            Batal
                        </a>

                        <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg font-semibold shadow-sm hover:bg-indigo-700 transition">
                            <i data-feather="save" class="w-4 h-4 inline mr-1"></i>
                            Simpan Perubahan
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <script>
        feather.replace();
    </script>
</x-app-layout>
