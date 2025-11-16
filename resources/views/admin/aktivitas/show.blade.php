<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detail Aktivitas Pegawai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">

                <header class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-green-900">
                        {{ $data->pegawai->user->name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Detail aktivitas untuk tanggal: {{ \Carbon\Carbon::parse($data->tanggal)->isoFormat('D MMMM YYYY') }}
                    </p>
                </header>

                <div class="p-6">
                    <dl class="divide-y divide-gray-200">
                        
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-base font-medium text-gray-500">Uraian Tugas</dt>
                            <dd class="mt-1 text-base text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $data->uraian_tugas }}</dd>
                        </div>
                        
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-base font-medium text-gray-500">Waktu Pelaksanaan</dt>
                            <dd class="mt-1 text-base text-gray-900 sm:mt-0 sm:col-span-2">{{ $data->waktu_pelaksanaan }}</dd>
                        </div>
                        
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-base font-medium text-gray-500">Hasil Pekerjaan</dt>
                            <dd class="mt-1 text-base text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $data->hasil_pekerjaan ?? '-' }}</dd>
                        </div>
                        
                        @if ($data->bukti_file)
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-base font-medium text-gray-500">Bukti Lampiran</dt>
                                <dd class="mt-1 text-base text-gray-900 sm:mt-0 sm:col-span-2">
                                    <a href="{{ Storage::url($data->bukti_file) }}" target="_blank" rel="noopener"
                                       class="inline-flex items-center font-medium text-green-600 hover:text-green-700">
                                        <i data-feather="external-link" class="w-4 h-4 mr-2"></i>
                                        Lihat Lampiran
                                    </a>
                                </dd>
                            </div>
                        @endif

                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-base font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-base text-gray-900 sm:mt-0 sm:col-span-2">
                                @if($data->status == 'menunggu')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                @elseif($data->status == 'disetujui')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @elseif($data->status == 'ditolak')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 capitalize">
                                        {{ $data->status }}
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-base font-medium text-gray-500">Catatan Atasan</dt>
                            <dd class="mt-1 text-base text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $data->komentar_atasan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <footer class="flex items-center gap-4 bg-gray-50 p-6 border-t border-gray-200">
                    <a href="{{ route('admin.aktivitas.index') }}"
                       class="inline-flex items-center bg-white text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-50 border border-gray-300 transition duration-150 ease-in-out text-base">
                        <i data-feather="arrow-left" class="w-5 h-5 mr-2 -ml-1"></i>
                        Kembali
                    </a>
                </footer>

            </div>
        </div>
    </div>
</x-app-layout>