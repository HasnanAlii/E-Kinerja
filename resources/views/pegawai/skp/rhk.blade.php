<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <i data-feather="plus-circle" class="w-5 h-5 text-indigo-600"></i>
            Tambah Hasil Kerja
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <form action="{{ route('skp.hasil_kerja.store', $skp->id) }}" method="POST">
                @csrf

                <div class="bg-white shadow-xl sm:rounded-2xl border border-gray-100 overflow-hidden">
                    
                    {{-- HEADER INFO --}}
                    <div class="bg-indigo-50 px-8 py-6 border-b border-indigo-100">
                        <h3 class="text-lg font-bold text-indigo-900">Formulir Rencana Hasil Kerja (RHK)</h3>
                        <p class="text-sm text-indigo-700 mt-1">
                            Menambahkan indikator kinerja untuk SKP Periode: <strong>{{ $skp->periode }}</strong>
                        </p>
                    </div>

                    <div class="p-8 space-y-8">

                        {{-- SECTION 1: KLASIFIKASI --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Jenis --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Hasil Kerja <span class="text-red-500">*</span></label>
                                <select name="jenis" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">
                                    <option value="Utama">Utama</option>
                                    <option value="Hasil Kerja">Tambahan (Hasil Kerja)</option>
                                </select>
                            </div>

                            {{-- Aspek --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Aspek Indikator <span class="text-red-500">*</span></label>
                                <select name="aspek" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">
                                    <option value="Kuantitas">Kuantitas</option>
                                    <option value="Kualitas">Kualitas</option>
                                    <option value="Waktu">Waktu</option>
                                </select>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- SECTION 2: RENCANA KINERJA --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Rencana Kinerja</h4>
                            
                            <div class="space-y-5">
                                {{-- RHK Pimpinan --}}
                                {{-- <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Rencana Hasil Kerja Pimpinan yang Diintervensi
                                    </label>
                                    <textarea name="rhk_pimpinan" rows="2" 
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                        placeholder="Contoh: Terwujudnya Pengelolaan Manajemen Perkantoran..."></textarea>
                                </div> --}}

                                {{-- RHK Individu --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Rencana Hasil Kerja (Individu) <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="rhk" rows="2" required
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                        placeholder="Masukan Rencana Hasil Kerja Anda"></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Pastikan penulisan RHK sama persis untuk Aspek Kuantitas, Kualitas, dan Waktu agar tergabung di tabel.</p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- SECTION 3: INDIKATOR & TARGET --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Indikator --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Indikator Kinerja Individu <span class="text-red-500">*</span></label>
                                <textarea name="indikator_kinerja" rows="3" required
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                    placeholder="Contoh: Jumlah dokumen administrasi keuangan..."></textarea>
                            </div>

                            {{-- Target --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Target / Ekspektasi <span class="text-red-500">*</span></label>
                                <textarea name="target" rows="3" required
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                    placeholder="Contoh: 9 Dokumen atau 100%"></textarea>
                            </div>
                        </div>

                        {{-- SECTION 4: REALISASI (Opsional / Diisi saat Evaluasi) --}}
                        <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                            <div class="flex items-center gap-2 mb-4">
                                <i data-feather="check-square" class="w-4 h-4 text-green-600"></i>
                                <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider">
                                    Realisasi & Evaluasi 
                                    <span class="text-xs font-normal text-gray-500  capitalize">(Opsional / Diisi Nanti)</span>
                                </h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Realisasi Berdasarkan Bukti Dukung</label>
                                    <textarea name="realisasi" rows="2"
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                        placeholder="Contoh: 1 Dokumen berdasarkan Laporan..."></textarea>
                                </div>
                                {{-- <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Umpan Balik Berkelanjutan</label>
                                    <textarea name="umpan_balik" rows="2"
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                        placeholder="Contoh: Pimpinan: Baik"></textarea>
                                </div> --}}
                            </div>
                        </div>

                    </div>

                    {{-- FOOTER ACTIONS --}}
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end items-center gap-4">
                        <a href="{{ route('skp.show', $skp->id) }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:bg-indigo-700 transition focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i data-feather="save" class="w-4 h-4 mr-2"></i>
                            Simpan Data
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
</x-app-layout>