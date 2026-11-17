<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Profil Karyawan
            </h2>
            
            <nav class="flex text-sm font-medium text-gray-500 mt-2 sm:mt-0">
                <a href="{{ route('admin.pegawai.index') }}" class="hover:text-indigo-600 transition">Data Karyawan</a>
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
                     Kembali ke Daftar Karyawan
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- KOLOM KIRI: KARTU IDENTITAS UTAMA --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100 relative">
                        <div class="h-24 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                        
                        <div class="px-6 pb-6 text-center relative">
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
                                </div>
                            </div>

                            {{-- Nama & Jabatan --}}
                            <h3 class="text-xl font-bold text-gray-900">{{ $data->user->name }}</h3>
                            <p class="text-sm text-indigo-600 font-medium mt-1">{{ $data->jabatan ?? '— Jabatan belum diisi —' }}</p>

                            <div class="mt-3 flex justify-center">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    {{ $data->status ?? 'Masih Bekerja' }}
                                </span>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-3">
                                <a href="{{ route('laporan.kehadiran.perpegawai', ['pegawai_id' => $data->id]) }}"
                                    target="_blank"
                                    class="flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                                    <i data-feather="file-text" class="w-4 h-4 mr-2"></i>
                                    Laporan Kehadiran PDF
                                </a>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <a href="{{ route('admin.pegawai.edit', $data->id) }}"
                                   class="flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                    <i data-feather="edit-2" class="w-4 h-4 mr-2"></i>
                                    Edit
                                </a>

                                <form action="{{ route('admin.pegawai.destroy', $data->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus karyawan ini?');">
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

                </div>

                {{-- KOLOM KANAN: DETAIL INFORMASI KARYAWAN --}}
                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center">
                            <i data-feather="briefcase" class="w-5 h-5 text-indigo-500 mr-3"></i>
                            <h3 class="font-bold text-gray-800">Data Karyawan</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Nama</span>
                                    <span class="block text-base font-semibold text-gray-800">{{ $data->user->name }}</span>
                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">NIK Karyawan</span>
                                    <span class="font-mono text-base font-bold bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded">
                                        {{ $data->nik_karyawan ?? $data->nip ?? $data->user->nik ?? '-' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Jenis Kelamin</span>
                                    <span class="block text-base font-semibold text-gray-800">{{ $data->user->jenis_kelamin ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Departemen</span>
                                    <span class="block text-base font-semibold text-gray-800">{{ $data->departemen ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Divisi</span>
                                    <span class="block text-base font-semibold text-gray-800">{{ $data->divisi ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Jabatan</span>
                                    <span class="block text-base font-semibold text-gray-800">{{ $data->jabatan ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Status</span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ in_array($data->status, ['Masih Bekerja', 'Aktif', null]) ? 'bg-emerald-100 text-emerald-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $data->status ?? 'Masih Bekerja' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Email Akun</span>
                                    <span class="block text-base font-semibold text-gray-800">{{ $data->user->email }}</span>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
