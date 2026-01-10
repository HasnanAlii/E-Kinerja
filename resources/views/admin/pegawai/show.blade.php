<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Profil Pegawai
            </h2>
            
            {{-- Breadcrumb kecil / Navigasi --}}
            <nav class="flex text-sm font-medium text-gray-500 mt-2 sm:mt-0">
                <a href="{{ route('admin.pegawai.index') }}" class="hover:text-indigo-600 transition">Pegawai</a>
                <span class="mx-2">/</span>
                <span class="text-indigo-600">Detail</span>
            </nav>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-start">
            <a href="{{ route('admin.pegawai.index') }}" class="text-gray-500 hover:text-gray-700 font-medium flex items-center transition mb-4">
                 <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                            Kembali ke Daftar
            </a>
        </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- KOLOM KIRI: KARTU IDENTITAS UTAMA --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Kartu Foto & Ringkasan --}}
                    <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100 relative">
                        {{-- Hiasan Background Atas --}}
                        <div class="h-24 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                        
                        <div class="px-6 pb-6 text-center relative">
                            {{-- Foto Profil --}}
                            <div class="-mt-12 mb-4 flex justify-center">
                                <div class="relative">
                                    @if ($data->user->profile_photo)
                                        <img src="{{ asset('storage/' . $data->user->profile_photo) }}"
                                             class="w-32 h-32 rounded-xl object-cover border-4 border-white shadow-md">
                                    @else
                                        <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center border-4 border-white shadow-md">
                                            <div class="w-full h-full rounded-full bg-indigo-50 flex items-center justify-center text-indigo-300">
                                                <i data-feather="user" class="w-12 h-12"></i>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    {{-- Status Badge (Absolute) --}}
                                    {{-- <span class="absolute bottom-1 right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full" title="Status: Aktif"></span> --}}
                                </div>
                            </div>

                            {{-- Nama & Jabatan --}}
                            <h3 class="text-xl font-bold text-gray-900">{{ $data->user->name }}</h3>
                            <p class="text-sm text-indigo-600 font-medium mt-1">{{ $data->jabatan ?? '— Belum set jabatan —' }}</p>
                            
                            {{-- Badge Bidang --}}
                            <div class="mt-3 flex justify-center">
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200 flex items-center">
                                    <i data-feather="briefcase" class="w-3 h-3 mr-1.5"></i>
                                    {{ $data->bidang->nama_bidang ?? '-' }}
                                </span>
                            </div>

                            {{-- Info Kontak Singkat --}}
                            <div class="mt-6 border-t border-gray-100 pt-4 space-y-3 text-left">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i data-feather="mail" class="w-4 h-4 mr-3 text-gray-400"></i>
                                    <span class="truncate">{{ $data->user->email }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i data-feather="phone" class="w-4 h-4 mr-3 text-gray-400"></i>
                                    <span>{{ $data->user->telp ?? '-' }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i data-feather="map-pin" class="w-4 h-4 mr-3 text-gray-400"></i>
                                    <span class="truncate">{{ Str::limit($data->user->alamat ?? '-', 30) }}</span>
                                </div>
                            </div>

                          <div class="mt-6 grid grid-cols-1 gap-3">

                        <a href="{{ route('laporan.kehadiran.perpegawaiadmin', $data->id) }}"
                            target="_blank"
                            class="flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                                <i data-feather="file-text" class="w-4 h-4 mr-2"></i>
                                Laporan Kehadiran PDF
                            </a>
                          </div>
                            {{-- Action Buttons --}}
                          <div class="mt-6 grid grid-cols-2 gap-3">

                        <!-- Export PDF -->
                    
                        <!-- Edit -->
                        <a href="{{ route('admin.pegawai.edit', $data->id) }}"
                        class="flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            <i data-feather="edit-2" class="w-4 h-4 mr-2"></i>
                            Edit
                        </a>

                        <!-- Hapus -->
                        <form action="{{ route('admin.pegawai.destroy', $data->id) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus pegawai ini?');">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full flex items-center justify-center px-4 py-2 bg-white text-red-600 border border-red-300 text-sm font-medium rounded-lg hover:bg-red-50 transition shadow-sm">
                                <i data-feather="trash-2" class="w-4 h-4 mr-2"></i>
                                Hapus
                            </button>
                        </form>

                    </div>

                        </div>
                    </div>

                    {{-- Widget Atasan --}}
                    <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Atasan Langsung</h4>
                        <div class="flex items-center">
                            <div class="bg-indigo-50 p-2 rounded-full mr-3">
                                <i data-feather="user-check" class="w-5 h-5 text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $data->atasan->name ?? '— Tidak Ada —' }}</p>
                                <p class="text-xs text-gray-500">{{ $data->atasan->jabatan ?? 'Atasan Unit' }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN: DETAIL INFORMASI --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- SECTION 1: INFORMASI PRIBADI --}}
                    <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center">
                            <i data-feather="user" class="w-5 h-5 text-indigo-500 mr-3"></i>
                            <h3 class="font-bold text-gray-800">Informasi Pribadi</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                                
                                {{-- Item Group --}}
                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Nomor Induk Kependudukan (NIK)</span>
                                    <span class="block text-base font-semibold text-gray-800">{{ $data->user->nik ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Jenis Kelamin</span>
                                     <span class="block text-base font-semibold text-gray-800">{{ $data->user->jenis_kelamin?? '-' }}</span>

                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Tempat Lahir</span>
                                    <span class="block text-base font-semibold text-gray-800">{{ $data->user->tempat_lahir ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Tanggal Lahir</span>
                                    <span class="block text-base font-semibold text-gray-800">
                                        {{ $data->user->tanggal_lahir ? \Carbon\Carbon::parse($data->user->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Agama</span>
                                    <span class="block text-base font-semibold text-gray-800">{{ $data->user->agama ?? '-' }}</span>
                                </div>

                                <div class="sm:col-span-2">
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Alamat Lengkap</span>
                                    <span class="block text-base text-gray-800 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        {{ $data->user->alamat ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: DATA KEPEGAWAIAN --}}
                    <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center">
                            <i data-feather="briefcase" class="w-5 h-5 text-indigo-500 mr-3"></i>
                            <h3 class="font-bold text-gray-800">Data Kepegawaian</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                                
                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">NIP / Nomor Pegawai</span>
                                    <div class="flex items-center">
                                        <span class="font-mono text-base font-bold text-gray-800 bg-indigo-50 text-indigo-700 px-2 py-1 rounded">
                                            {{ $data->nip ?? '-' }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Status Kepegawaian</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $data->status == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $data->status ?? '-' }}
                                    </span>
                                </div>

                              <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Masa Kerja</span>
                                    <span class="block text-base font-semibold text-gray-800">
                                        @if ($data->tanggal_masuk)
                                            @php
                                                $masuk = \Carbon\Carbon::parse($data->tanggal_masuk);
                                                $now   = \Carbon\Carbon::now();
                                                $selisih = $masuk->diff($now);
                                            @endphp
                                            {{ $selisih->y }} Tahun {{ $selisih->m }} Bulan {{ $selisih->d }} Hari
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>

                                {{-- <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Akhir Kontrak</span>
                                    <span class="block text-base font-semibold text-gray-800">
                                        {{ $data->masa_kontrak ? \Carbon\Carbon::parse($data->masa_kontrak)->translatedFormat('d F Y') : '— (Permanen)' }}
                                    </span>
                                </div> --}}

                            </div>
                        </div>
                    </div>

               

                </div>
            </div>

        </div>
    </div>
</x-app-layout>