<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Profil Atasan
            </h2>

            {{-- Breadcrumb --}}
            <nav class="flex text-sm font-medium text-gray-500 mt-2 sm:mt-0">
                <a href="{{ route('admin.atasan.index') }}" class="hover:text-indigo-600 transition">Atasan</a>
                <span class="mx-2">/</span>
                <span class="text-indigo-600">Detail</span>
            </nav>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                    {{-- BUTTON KEMBALI --}}
                    <div>
                        <a href="{{ route('admin.atasan.index') }}"
                            class="text-gray-500 hover:text-gray-700 font-medium flex items-center transition mb-4">
                            <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                            Kembali ke Daftar
                        </a>
                    </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- =========================== --}}
                {{-- KOLOM KIRI: IDENTITAS UTAMA --}}
                {{-- =========================== --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- KARTU IDENTITAS --}}
                    <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100 relative ">
                        
                        {{-- Gradient Background --}}
                        <div class="h-24 bg-gradient-to-r from-indigo-500 to-purple-500"></div>

                        <div class="px-6 pb-9 text-center relative">

                            {{-- Foto Profil --}}
                            <div class="-mt-12 mb-4 flex justify-center">
                                <div class="relative">

                                    @if ($atasan->user->profile_photo)
                                        <img src="{{ asset('storage/' . $atasan->user->profile_photo) }}"
                                            class="w-32 h-32 rounded-xl object-cover border-4 border-white shadow-md">
                                    @else
                                        <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center border-4 border-white shadow-md">
                                            <div class="w-full h-full rounded-full bg-indigo-50 flex items-center justify-center text-indigo-300">
                                                <i data-feather="user" class="w-12 h-12"></i>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Status Badge --}}
                                    {{-- <span class="absolute bottom-1 right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full"></span> --}}
                                </div>
                            </div>

                            {{-- Nama --}}
                            <h3 class="text-xl font-bold text-gray-900">
                                {{ $atasan->user->name }}
                            </h3>

                            {{-- Jabatan --}}
                            <p class="text-sm text-indigo-600 font-medium mt-1">
                                {{ $atasan->jabatan ?? '— Jabatan belum diisi —' }}
                            </p>

                            {{-- Bidang Badge --}}
                            <div class="mt-3 flex justify-center">
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200 flex items-center">
                                    <i data-feather="layers" class="w-3 h-3 mr-1.5"></i>
                                    {{ $atasan->bidang->nama_bidang ?? '-' }}
                                </span>
                            </div>

                            {{-- INFORMASI KONTAK --}}
                            <div class="mt-6 border-t border-gray-100 pt-4 space-y-3 text-left">

                                <div class="flex items-center text-sm text-gray-600">
                                    <i data-feather="mail" class="w-4 h-4 mr-3 text-gray-400"></i>
                                    <span class="truncate">{{ $atasan->user->email }}</span>
                                </div>

                                <div class="flex items-center text-sm text-gray-600">
                                    <i data-feather="phone" class="w-4 h-4 mr-3 text-gray-400"></i>
                                    <span>{{ $atasan->user->telp ?? '-' }}</span>
                                </div>

                                <div class="flex items-center text-sm text-gray-600">
                                    <i data-feather="map-pin" class="w-4 h-4 mr-3 text-gray-400"></i>
                                    <span class="truncate">
                                        {{ Str::limit($atasan->user->alamat ?? '-', 40) }}
                                    </span>
                                </div>
                            </div>

                            {{-- ACTION BUTTONS --}}
                            <div class="mt-16 grid grid-cols-2 gap-3">
                                <a href="{{ route('admin.atasan.edit', $atasan->id) }}"
                                   class="flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                    <i data-feather="edit-2" class="w-4 h-4 mr-2"></i> Edit
                                </a>

                                <form action="{{ route('admin.atasan.destroy', $atasan->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus data atasan ini?');">
                                    @csrf @method('DELETE')
                                    <button class="w-full flex items-center justify-center px-4 py-2 bg-white text-red-600 border border-red-200 text-sm font-medium rounded-lg hover:bg-red-50 transition">
                                        <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>


                </div>

                {{-- =========================== --}}
                {{-- KOLOM KANAN: DETAIL INFORMASI --}}
                {{-- =========================== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- SECTION: INFORMASI PRIBADI --}}
                    <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">

                        <div class="px-6 py-4  border-b bg-gray-50/50 flex items-center">
                            <i data-feather="user" class="w-5 h-5 text-indigo-500 mr-3"></i>
                            <h3 class="font-bold text-gray-800">Informasi Pribadi</h3>
                        </div>

                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">

                            <div>
                                <span class="label">NIK</span>
                                <span class="value">{{ $atasan->user->nik ?? '-' }}</span>
                            </div>

                            <div>
                                <span class="label">Jenis Kelamin</span>
                                <span class="value">
                                    @if($atasan->user->jenis_kelamin == 'L') Laki-laki
                                    @elseif($atasan->user->jenis_kelamin == 'P') Perempuan
                                    @else - @endif
                                </span>
                            </div>

                            <div>
                                <span class="label">Tempat Lahir</span>
                                <span class="value">{{ $atasan->user->tempat_lahir ?? '-' }}</span>
                            </div>

                            <div>
                                <span class="label">Tanggal Lahir</span>
                                <span class="value">
                                {{ $atasan->user->tanggal_lahir ? \Carbon\Carbon::parse($atasan->user->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                                </span>
                            </div>

                            <div>
                                <span class="label">Agama</span>
                                <span class="value">{{ $atasan->user->agama ?? '-' }}</span>
                            </div>

                            <div class="sm:col-span-2">
                                <span class="label">Alamat</span>
                                <span class="block value bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    {{ $atasan->user->alamat ?? '-' }}
                                </span>
                            </div>

                        </div>
                    </div>


                    {{-- SECTION: STRUKTURAL & KEPEGAWAIAN --}}
                    <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">

                        <div class="px-6 py-4 border-b bg-gray-50/50 flex items-center">
                            <i data-feather="briefcase" class="w-5 h-5 text-indigo-500 mr-3"></i>
                            <h3 class="font-bold text-gray-800">Data Kepegawaian</h3>
                        </div>

                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">

                      <div>
                            <span class="label">NIP</span>
                            <span class="value font-mono bg-indigo-50 text-indigo-700 px-2 py-1 rounded">
                                {{ $atasan->nip ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="label">Golongan</span>
                            <span class="value">
                                {{ $atasan->golongan ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="label">Status</span>
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $atasan->status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $atasan->status ?? '-' }}
                            </span>
                        </div>

                            <div>
                                <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Masa Kerja</span>
                                <span class="block text-base font-semibold text-gray-800">
                                    @if ($atasan->tanggal_masuk)
                                        @php
                                            $masuk = \Carbon\Carbon::parse($atasan->tanggal_masuk);
                                            $now   = \Carbon\Carbon::now();
                                            $selisih = $masuk->diff($now);
                                        @endphp
                                        {{ $selisih->y }} Tahun {{ $selisih->m }} Bulan {{ $selisih->d }} Hari
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>

{{-- 
                            <div>
                                <span class="label">Akhir Kontrak</span>
                                <span class="value">
                                    {{ $atasan->masa_kontrak ? \Carbon\Carbon::parse($atasan->masa_kontrak)->translatedFormat('d F Y') : '— (Permanen)' }}
                                </span>
                            </div> --}}

                        </div>
                    </div>



                </div>
            </div>

        </div>
    </div>
</x-app-layout>
