<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Tambah Pegawai Non-PNS
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl sm:rounded-xl border border-gray-200">
                
                {{-- Header Card --}}
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center space-x-3 rounded-t-xl">
                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                        <i data-feather="user-plus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Form Tambah Pegawai</h3>
                        <p class="text-sm text-gray-500">Lengkapi data berikut untuk menambahkan pegawai baru ke sistem.</p>
                    </div>
                </div>

                <form action="{{ route('admin.pegawai.store') }}" 
                      method="POST" enctype="multipart/form-data" 
                      class="px-6 pb-6 space-y-10">
                    @csrf

                    {{-- ====================== --}}
                    {{-- SECTION - Akun User --}}
                    {{-- ====================== --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center space-x-2">
                            <i data-feather="lock" class="w-5 h-5 text-indigo-600"></i>
                            <span>Akun Pegawai</span>
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Informasi ini digunakan pegawai untuk login ke sistem.
                        </p>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="md:col-span-2">
                                <label class="text-base font-medium text-gray-700">Nama Pegawai</label>
                                <input type="text" name="nama" value="{{ old('nama') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base">
                                @error('nama') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-base font-medium text-gray-700">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base">
                                @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-base font-medium text-gray-700">Password</label>
                                <input type="password" name="password"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base">
                                @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ====================== --}}
                    {{-- SECTION - Detail Pegawai --}}
                    {{-- ====================== --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center space-x-2">
                            <i data-feather="info" class="w-5 h-5 text-indigo-600"></i>
                            <span>Detail Pegawai</span>
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Informasi kepegawaian untuk kebutuhan administrasi.
                        </p>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Bidang --}}
                            <div>
                                <label class="text-base font-medium text-gray-700">Bidang</label>
                                <select name="bidang_id"
                                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base">
                                    <option value="">-- Pilih Bidang --</option>
                                    @foreach ($bidang as $b)
                                        <option value="{{ $b->id }}" @selected(old('bidang_id') == $b->id)>
                                            {{ $b->nama_bidang }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bidang_id') 
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p> 
                                @enderror
                            </div>

                            {{-- Atasan --}}
                            <div>
                                <label class="text-base font-medium text-gray-700">Atasan</label>
                                <select name="atasan_id"
                                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base">
                                    <option value="">-- Pilih Atasan --</option>
                                    @foreach ($atasan as $a)
                                        <option value="{{ $a->id }}" @selected(old('atasan_id') == $a->id)>
                                            {{ $a->name }} — {{ $a->jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('atasan_id') 
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p> 
                                @enderror
                            </div>

                            {{-- Jabatan --}}
                            <div>
                                <label class="text-base font-medium text-gray-700">Jabatan</label>
                                <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base">
                            </div>

                            {{-- NIP --}}
                            <div>
                                <label class="text-base font-medium text-gray-700">NIP</label>
                                <input type="text" name="nip" value="{{ old('nip') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base">
                            </div>

                            {{-- Foto --}}
                            <div class="md:col-span-2">
                                <label class="text-base font-medium text-gray-700">Foto Pegawai (Opsional)</label>
                                
                                <input type="file" name="foto"
                                       class="mt-1 w-full text-base text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none
                                              file:bg-gray-100 file:border-0 file:px-4 file:py-2 file:mr-4 file:text-base file:font-medium">
                                
                                @error('foto') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ====================== --}}
                    {{-- FOOTER BUTTONS --}}
                    {{-- ====================== --}}
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.pegawai.index') }}" 
                           class="text-gray-600 hover:text-gray-900 mr-4 text-base">
                            Batal
                        </a>

                        <button type="submit" 
                                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm text-base transition focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <i data-feather="save" class="w-5 h-5 mr-2"></i>
                            Simpan Pegawai
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
