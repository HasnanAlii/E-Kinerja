<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Tambah Pegawai Non-PNS
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-xl border border-gray-200">

                {{-- Header --}}
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center space-x-3 rounded-t-xl">
                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                        <i data-feather="user-plus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Form Tambah Pegawai</h3>
                        <p class="text-sm text-gray-500">Lengkapi data berikut untuk menambahkan pegawai baru.</p>
                    </div>
                </div>

                <form action="{{ route('admin.pegawai.store') }}" method="POST" enctype="multipart/form-data"
                      class="px-6 pb-6 space-y-10">
                    @csrf

                    {{-- ================================================= --}}
                    {{-- SECTION 1 — Akun User --}}
                    {{-- ================================================= --}}
                    <div>
                        <h3 class="text-lg font-bold flex items-center text-gray-900 space-x-2">
                            <i data-feather="lock" class="w-5 h-5 text-indigo-600"></i>
                            <span>Akun Pegawai</span>
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">Digunakan untuk login ke sistem.</p>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Nama --}}
                            <div class="md:col-span-2">
                                <label class="font-medium text-gray-700">Nama Pegawai</label>
                                <input type="text" name="nama" value="{{ old('nama') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="font-medium text-gray-700">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="font-medium text-gray-700">Password</label>
                                <input type="password" name="password"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                        </div>
                    </div>

                    {{-- ================================================= --}}
                    {{-- SECTION 2 — Data Pribadi User --}}
                    {{-- ================================================= --}}
                    <div>
                        <h3 class="text-lg font-bold flex items-center text-gray-900 space-x-2">
                            <i data-feather="user" class="w-5 h-5 text-indigo-600"></i>
                            <span>Data Pribadi</span>
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">Informasi ini disimpan pada tabel user.</p>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- NIK --}}
                            <div>
                                <label class="font-medium text-gray-700">NIK</label>
                                <input type="text" name="nik" value="{{ old('nik') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div>
                                <label class="font-medium text-gray-700">Jenis Kelamin</label>
                                <select name="jenis_kelamin"
                                        class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki" @selected(old('jenis_kelamin')=='Laki-laki')>Laki-laki</option>
                                    <option value="Perempuan" @selected(old('jenis_kelamin')=='Perempuan')>Perempuan</option>
                                </select>
                            </div>

                            {{-- Tempat Lahir --}}
                            <div>
                                <label class="font-medium text-gray-700">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Tanggal Lahir --}}
                            <div>
                                <label class="font-medium text-gray-700">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Agama --}}
                            <div>
                                <label class="font-medium text-gray-700">Agama</label>
                                <input type="text" name="agama" value="{{ old('agama') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Telepon --}}
                            <div>
                                <label class="font-medium text-gray-700">Telepon</label>
                                <input type="text" name="telp" value="{{ old('telp') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Alamat --}}
                            <div class="md:col-span-2">
                                <label class="font-medium text-gray-700">Alamat</label>
                                <textarea name="alamat" rows="2"
                                          class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('alamat') }}</textarea>
                            </div>

                            {{-- Foto Profil User --}}
                            <div class="md:col-span-2">
                                <label class="font-medium text-gray-700">Foto (Opsional)</label>
                                <input type="file" name="profile_photo"
                                       class="mt-1 w-full border-gray-300 rounded-lg file:bg-gray-100 file:px-4 file:py-2 file:border-0">
                            </div>

                        </div>
                    </div>

                    {{-- ================================================= --}}
                    {{-- SECTION 3 — Detail Pegawai --}}
                    {{-- ================================================= --}}
                    <div>
                        <h3 class="text-lg font-bold flex items-center text-gray-900 space-x-2">
                            <i data-feather="briefcase" class="w-5 h-5 text-indigo-600"></i>
                            <span>Detail Pegawai</span>
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">Informasi kepegawaian untuk administrasi.</p>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Bidang --}}
                            <div>
                                <label class="font-medium text-gray-700">Bidang</label>
                                <select name="bidang_id"
                                        class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Bidang --</option>
                                    @foreach ($bidang as $b)
                                        <option value="{{ $b->id }}" @selected(old('bidang_id')==$b->id)>
                                            {{ $b->nama_bidang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Atasan --}}
                            <div>
                                <label class="font-medium text-gray-700">Atasan</label>
                                <select name="atasan_id"
                                        class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Atasan --</option>
                                    @foreach ($atasan as $a)
                                        <option value="{{ $a->id }}" @selected(old('atasan_id')==$a->id)>
                                            {{ $a->name }} — {{ $a->jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Jabatan --}}
                            <div>
                                <label class="font-medium text-gray-700">Jabatan</label>
                                <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- NIP --}}
                            <div>
                                <label class="font-medium text-gray-700">NIP</label>
                                <input type="text" name="nip" value="{{ old('nip') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="font-medium text-gray-700">Status Kepegawaian</label>
                                <select name="status"
                                        class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>


                            {{-- Tanggal Masuk --}}
                            <div>
                                <label class="font-medium text-gray-700">Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            </div>


                            {{-- Masa Kontrak --}}
                            {{-- <div>
                                <label class="font-medium text-gray-700">Masa Kontrak</label>
                                <input type="date" name="masa_kontrak" value="{{ old('masa_kontrak') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div> --}}


                        
                    </div>

                    {{-- ================================================= --}}
                    {{-- FOOTER --}}
                    {{-- ================================================= --}}
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.pegawai.index') }}"
                           class="text-gray-600 hover:text-gray-900 mr-4 text-base">
                            Batal
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm text-base transition focus:ring-2 focus:ring-indigo-500">
                            <i data-feather="save" class="w-5 h-5 mr-2"></i>
                            Simpan Pegawai
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
