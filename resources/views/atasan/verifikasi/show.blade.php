<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detail Aktivitas Pegawai
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- CARD UTAMA --}}
            <div class="bg-white shadow-xl sm:rounded-xl border border-gray-200 overflow-hidden">

                {{-- HEADER CARD --}}
                <div class="px-6 py-5 flex items-center gap-3 border-b bg-gray-50">
                    <div class="p-3 bg-indigo-100 text-indigo-600 rounded-lg">
                        <i data-feather="clipboard" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">
                            Detail Aktivitas — {{ $data->pegawai->user->name }}
                        </h3>
                        <p class="text-sm text-gray-500">Informasi lengkap laporan aktivitas harian.</p>
                    </div>
                </div>

                <div class="px-6 py-6 space-y-10">

                    {{-- ================================= --}}
                    {{-- SECTION : DATA PEGAWAI --}}
                    {{-- ================================= --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        {{-- FOTO --}}
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('storage/' . ($data->pegawai->foto ?? 'default.png')) }}"
                                 class="h-20 w-20 rounded-full object-cover border shadow">

                            <div>
                                <div class="font-semibold text-gray-900 text-lg">
                                    {{ $data->pegawai->user->name }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $data->pegawai->jabatan ?? '-' }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ $data->pegawai->bidang->nama_bidang ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- ================================= --}}
                    {{-- SECTION : DETAIL AKTIVITAS --}}
                    {{-- ================================= --}}
                    <div class="border rounded-lg p-5 bg-gray-50 space-y-6">

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Tanggal</p>
                            <p class="text-gray-900 text-base font-semibold">
                                {{ \Carbon\Carbon::parse($data->tanggal)->isoFormat('D MMMM YYYY') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Waktu Pelaksanaan</p>
                            <p class="text-gray-900 text-base font-semibold">
                                {{ $data->waktu_pelaksanaan }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Uraian Tugas</p>
                            <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">
                                {{ $data->uraian_tugas }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Hasil Pekerjaan</p>
                            <p class="text-gray-800 whitespace-pre-wrap">
                                {{ $data->hasil_pekerjaan ?? '-' }}
                            </p>
                        </div>

                        {{-- BUKTI FILE --}}
                        @if ($data->bukti_file)
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Lampiran / Bukti Aktivitas</p>
                                <a href="{{ Storage::url($data->bukti_file) }}" target="_blank"
                                   class="inline-flex items-center mt-2 px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700">
                                    <i data-feather="paperclip" class="w-4 h-4 mr-2"></i>
                                    Lihat Lampiran
                                </a>
                            </div>
                        @endif

                    </div>



                    {{-- ================================= --}}
                    {{-- SECTION : TINDAKAN VERIFIKASI --}}
                    {{-- ================================= --}}
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">
                            Tindakan Verifikasi
                        </h3>
                        <p class="text-sm text-gray-600">
                            Pilih salah satu tindakan berikut untuk memproses aktivitas pegawai ini.
                        </p>

                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">

                            {{-- BUTTON APPROVE --}}
                            <button onclick="openModal('approve', '{{ route('atasan.verifikasi.approve', $data->id) }}')"
                                class="inline-flex items-center justify-center px-4 py-2 w-full text-white bg-green-600 hover:bg-green-700 rounded-lg shadow">
                                <i data-feather="check" class="w-5 h-5 mr-2"></i>
                                Setujui
                            </button>

                            {{-- BUTTON TOLAK --}}
                            <button onclick="openModal('reject', '{{ route('atasan.verifikasi.reject', $data->id) }}')"
                                class="inline-flex items-center justify-center px-4 py-2 w-full text-white bg-red-600 hover:bg-red-700 rounded-lg shadow">
                                <i data-feather="x" class="w-5 h-5 mr-2"></i>
                                Tolak
                            </button>

                            {{-- BUTTON REVISI --}}
                            <button onclick="openModal('revisi', '{{ route('atasan.verifikasi.revisi', $data->id) }}')"
                                class="inline-flex items-center justify-center px-4 py-2 w-full text-white bg-yellow-500 hover:bg-yellow-600 rounded-lg shadow">
                                <i data-feather="edit" class="w-5 h-5 mr-2"></i>
                                Minta Revisi
                            </button>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>


    @include('atasan.verifikasi.partials.modal')

</x-app-layout>
