<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i data-feather="file-plus" class="w-6 h-6 text-indigo-600"></i>
            Buat Sasaran Kinerja Pegawai (SKP)
        </h2>
    </x-slot>

    <div>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- CARD UTAMA --}}
            <div class="bg-white shadow-xl sm:rounded-2xl border border-gray-100 overflow-hidden">
                
                {{-- INFO BANNER --}}
                <div class="px-8 py-6 bg-indigo-50 border-b border-indigo-100 flex items-start gap-4">
                    <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600 shrink-0">
                        <i data-feather="info" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-indigo-900">Informasi SKP</h3>
                        <p class="text-sm text-indigo-700 mt-1">
                            Silakan isi periode dan target kinerja organisasi. Data ini akan menjadi acuan dalam penilaian kinerja pegawai.
                        </p>
                    </div>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('skp.store') }}" class="space-y-8">
                        @csrf

                        {{-- SECTION 1: PERIODE --}}
                        <div>
                            <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2 uppercase tracking-wide">
                                <i data-feather="calendar" class="w-4 h-4 text-gray-400"></i>
                                Periode Penilaian
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Nama Periode --}}
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Periode <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input name="periode" required
                                               class="w-full pl-4 pr-4 py-2.5 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all"
                                               placeholder="Contoh: Januari – Juni 2025">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Nama yang mudah dikenali untuk laporan.</p>
                                </div>

                                {{-- Tanggal Mulai --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai"
                                           class="w-full py-2.5 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all cursor-pointer">
                                </div>

                                {{-- Tanggal Selesai --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai"
                                           class="w-full py-2.5 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- SECTION 2: KINERJA ORGANISASI --}}
                        <div>
                            <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2 uppercase tracking-wide">
                                <i data-feather="briefcase" class="w-4 h-4 text-gray-400"></i>
                                Target Organisasi
                            </h4>

                            <div class="space-y-6">
                                {{-- Capaian Organisasi --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Capaian Kinerja Organisasi
                                        <span class="text-xs font-normal text-gray-400 ml-1">(Opsional)</span>
                                    </label>
                                    <textarea name="capaian_kinerja_organisasi"
                                              class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all"
                                              rows="3"
                                              placeholder="Deskripsikan target kinerja organisasi yang ingin dicapai..."></textarea>
                                </div>

                                {{-- Pola Distribusi --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Pola Distribusi
                                        <span class="text-xs font-normal text-gray-400 ml-1">(Opsional)</span>
                                    </label>
                                    <textarea name="pola_distribusi"
                                              class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all"
                                              rows="3"
                                              placeholder="Jelaskan pola distribusi kinerja jika ada..."></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                            <a href="{{ url()->previous() }}" 
                               class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold shadow-md shadow-indigo-200 hover:bg-indigo-700 transition-all transform active:scale-95">
                                <i data-feather="save" class="w-4 h-4 mr-2"></i>
                                Simpan Data
                            </button>
                        </div>

                    </form>
                </div>
            </div>

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