<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Detail Pengajuan Izin / Sakit
        </h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">

            {{-- HEADER CARD --}}
            <div class="px-6 py-5 bg-gray-50 border-b flex items-center gap-3">
                <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                    <i data-feather="file-text" class="w-6 h-6"></i>
                </span>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Detail Pengajuan Izin / Sakit
                    </h3>
                    <p class="text-sm text-gray-500">Informasi lengkap pengajuan dari pegawai.</p>
                </div>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-5">

                {{-- Pegawai --}}
                <div class="flex items-center gap-4">
                     @if ($izin->pegawai->user->profile_photo)
                           <img src="{{ asset('storage/' . ($izin->pegawai->user->profile_photo )) }}"
                         class="h-14 w-14 rounded-full object-cover border shadow-sm">
                    @else
                       <div class="p-3 bg-indigo-50 text-indigo-600 rounded-full">
                            <i data-feather="user" class="w-6 h-6"></i>
                        </div>
                    @endif
                
                    <div>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $izin->pegawai->user->name }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $izin->pegawai->jabatan ?? '-' }}</p>
                    </div>
                </div>

                {{-- Jenis Izin --}}
                <div>
                    <p class="text-sm text-gray-500">Jenis Pengajuan</p>
                    <p class="text-base font-medium capitalize text-gray-900">
                        {{ $izin->jenis }}
                    </p>
                </div>

                {{-- Tanggal --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Mulai</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->isoFormat('D MMMM YYYY') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Tanggal Selesai</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->isoFormat('D MMMM YYYY') }}
                        </p>
                    </div>
                </div>

                {{-- Lampiran --}}
                @if ($izin->file_surat)
                    <div>
                        <p class="text-sm text-gray-500">Lampiran Bukti</p>
                        <a href="{{ asset('storage/' . $izin->file_surat) }}" target="_blank"
                           class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                            <i data-feather="paperclip" class="w-4 h-4 mr-2"></i>
                            Lihat Lampiran
                        </a>
                    </div>
                @endif

                {{-- Status --}}
                <div>
                    <p class="text-sm text-gray-500">Status Pengajuan</p>

                    @php
                        $badge = [
                            'menunggu' => 'bg-yellow-100 text-yellow-700',
                            'disetujui' => 'bg-green-100 text-green-700',
                            'ditolak' => 'bg-red-100 text-red-700',
                        ];
                    @endphp

                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $badge[$izin->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($izin->status) }}
                    </span>
                </div>

                {{-- CATATAN ATASAN (Jika ditolak) --}}
                @if ($izin->komentar_atasan)
                    <div>
                        <p class="text-sm text-gray-500">Catatan Atasan</p>
                        <div class="p-3 bg-gray-50 border rounded-lg text-gray-800 whitespace-pre-wrap">
                            {{ $izin->komentar_atasan }}
                        </div>
                    </div>
                @endif

                {{-- TOMBOL AKSI --}}
                <div class="pt-4 flex gap-4 justify-end">
                    <a href="{{ route('atasan.izin.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300
                            text-gray-700 rounded-lg shadow text-base">
                        <i data-feather="arrow-left" class="w-5 h-5 mr-2"></i>
                        Kembali
                    </a>
                    @if ($izin->status == 'menunggu')  
                    <form action="{{ route('atasan.izin.reject', $izin->id) }}" method="POST">
                        @csrf
                        <button class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700
                                      text-white rounded-lg shadow text-base">
                            <i data-feather="x" class="w-5 h-5 mr-2"></i>
                            Tolak
                        </button>
                    </form>
                    <form action="{{ route('atasan.izin.approve', $izin->id) }}" method="POST">
                        @csrf
                        <button class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700
                                      text-white rounded-lg shadow text-base">
                            <i data-feather="check" class="w-5 h-5 mr-2"></i>
                            Setujui
                        </button>
                    </form>
                    @endif

                </div>
                
                
            </div>
        </div>

    </div>
</x-app-layout>
