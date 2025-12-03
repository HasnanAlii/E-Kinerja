<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Tambah Atasan
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
                        <h3 class="text-lg font-bold text-gray-800">Form Tambah Atasan</h3>
                        <p class="text-sm text-gray-500">Lengkapi data berikut untuk menambahkan atasan baru.</p>
                    </div>
                </div>

                <form action="{{ route('admin.atasan.store') }}" method="POST" enctype="multipart/form-data"
                      class="px-6 pb-6 space-y-10">
                    @csrf


                    {{-- ================================================= --}}
                    {{-- SECTION 1 — Akun User --}}
                    {{-- ================================================= --}}
                    <div>
                        <h3 class="text-lg font-bold flex items-center text-gray-900 space-x-2">
                            <i data-feather="lock" class="w-5 h-5 text-indigo-600"></i>
                            <span>Akun Atasan</span>
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">Digunakan untuk login ke sistem.</p>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Nama --}}
                            <div class="md:col-span-2">
                                <label class="font-medium text-gray-700">
                                    Nama Lengkap <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="nama" required value="{{ old('nama') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="font-medium text-gray-700">
                                    Email <span class="text-red-600">*</span>
                                </label>
                                <input type="email" name="email" required value="{{ old('email') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="font-medium text-gray-700">
                                    Password <span class="text-red-600">*</span>
                                </label>
                                <input type="password" name="password" required
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                        </div>
                    </div>


                    {{-- ================================================= --}}
                    {{-- SECTION 2 — Data Pribadi (Opsional) --}}
                    {{-- ================================================= --}}
                    <div>
                        <h3 class="text-lg font-bold flex items-center text-gray-900 space-x-2">
                            <i data-feather="user" class="w-5 h-5 text-indigo-600"></i>
                            <span>Data Pribadi</span>
                        </h3>

                        <p class="mt-1 text-sm text-gray-500 italic">
                            Data pribadi bersifat opsional dan dapat diisi oleh atasan setelah akun dibuat.
                        </p>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- NIK --}}
                            <div>
                                <label class="font-medium text-gray-700">NIK (Opsional)</label>
                                <input type="text" name="nik" value="{{ old('nik') }}"
                                       class="mt-1 w-full rounded-md border-gray-300">
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div>
                                <label class="font-medium text-gray-700">Jenis Kelamin (Opsional)</label>
                                <select name="jenis_kelamin"
                                        class="mt-1 w-full rounded-md border-gray-300">
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki" @selected(old('jenis_kelamin')=='Laki-laki')>Laki-laki</option>
                                    <option value="Perempuan" @selected(old('jenis_kelamin')=='Perempuan')>Perempuan</option>
                                </select>
                            </div>

                            {{-- Tempat Lahir --}}
                            <div>
                                <label class="font-medium text-gray-700">Tempat Lahir (Opsional)</label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                                       class="mt-1 w-full rounded-md border-gray-300">
                            </div>

                            {{-- Tanggal Lahir --}}
                            <div>
                                <label class="font-medium text-gray-700">Tanggal Lahir (Opsional)</label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                       class="mt-1 w-full rounded-md border-gray-300">
                            </div>

                            {{-- Agama --}}
                            <div>
                                <label class="font-medium text-gray-700">Agama (Opsional)</label>
                                <input type="text" name="agama" value="{{ old('agama') }}"
                                       class="mt-1 w-full rounded-md border-gray-300">
                            </div>

                            {{-- Telepon --}}
                            <div>
                                <label class="font-medium text-gray-700">Telepon (Opsional)</label>
                                <input type="text" name="telp" value="{{ old('telp') }}"
                                       class="mt-1 w-full rounded-md border-gray-300">
                            </div>

                            {{-- Alamat --}}
                            <div class="md:col-span-2">
                                <label class="font-medium text-gray-700">Alamat (Opsional)</label>
                                <textarea name="alamat" rows="2"
                                          class="mt-1 w-full rounded-md border-gray-300">{{ old('alamat') }}</textarea>
                            </div>

                            {{-- Foto Profil --}}
                            <div class="md:col-span-2">
                                <label class="font-medium text-gray-700">Foto Profil (Opsional)</label>

                                <div class="mt-2 flex items-center">

                                    <img id="previewImage"
                                         src="{{ asset('images/default-preview.png') }}"
                                         class="hidden w-20 h-20 rounded-lg object-cover mr-4 border border-gray-300">

                                    <label class="cursor-pointer bg-blue-50 text-blue-700 border border-blue-300 hover:bg-blue-100 
                                                   transition px-4 py-2 rounded-lg flex items-center gap-2 font-medium shadow-sm">

                                        <i data-feather="upload" class="w-5 h-5"></i>
                                        <span>Pilih Foto</span>

                                        <input type="file" name="profile_photo" id="profile_photo"
                                               class="hidden" accept="image/*" onchange="previewFile(event)">
                                    </label>
                                </div>

                                <script>
                                    function previewFile(event) {
                                        const image = document.getElementById('previewImage');
                                        image.src = URL.createObjectURL(event.target.files[0]);
                                        image.classList.remove('hidden');
                                    }
                                </script>
                            </div>

                        </div>
                    </div>


                    {{-- ================================================= --}}
                    {{-- SECTION 3 — Detail Atasan --}}
                    {{-- ================================================= --}}
                    <div>
                        <h3 class="text-lg font-bold flex items-center text-gray-900 space-x-2">
                            <i data-feather="briefcase" class="w-5 h-5 text-indigo-600"></i>
                            <span>Detail Atasan</span>
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">Informasi jabatan untuk struktur organisasi.</p>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Bidang --}}
                            <div>
                                <label class="font-medium text-gray-700">
                                    Bidang <span class="text-red-600">*</span>
                                </label>
                                <select name="bidang_id" required
                                        class="mt-1 w-full rounded-md border-gray-300">
                                    <option value="">-- Pilih Bidang --</option>
                                    @foreach ($bidang as $b)
                                        <option value="{{ $b->id }}" @selected(old('bidang_id')==$b->id)>
                                            {{ $b->nama_bidang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Jabatan --}}
                            <div>
                                <label class="font-medium text-gray-700">
                                    Jabatan <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="jabatan" required value="{{ old('jabatan') }}"
                                       class="mt-1 w-full rounded-md border-gray-300">
                            </div>

                            {{-- NIP --}}
                            <div>
                                <label class="font-medium text-gray-700">NIP (Opsional)</label>
                                <input type="text" name="nip" value="{{ old('nip') }}"
                                       class="mt-1 w-full rounded-md border-gray-300">
                            </div>

                            {{-- Golongan --}}
                            <div>
                                <label class="font-medium text-gray-700">Golongan (Opsional)</label>
                                <input type="text" name="golongan" value="{{ old('golongan') }}"
                                       class="mt-1 w-full rounded-md border-gray-300">
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="font-medium text-gray-700">Status Kepegawaian</label>
                                <select name="status"
                                        class="mt-1 w-full rounded-md border-gray-300">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Aktif" @selected(old('status')=='Aktif')>Aktif</option>
                                    <option value="Tidak Aktif" @selected(old('status')=='Tidak Aktif')>Tidak Aktif</option>
                                </select>
                            </div>

                            {{-- Tanggal Masuk --}}
                            <div>
                                <label class="font-medium text-gray-700">Tanggal Masuk (Opsional)</label>
                                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}"
                                       class="mt-1 w-full rounded-md border-gray-300">
                            </div>

                        </div>
                    </div>


                    {{-- FOOTER --}}
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.atasan.index') }}"
                           class="text-gray-600 hover:text-gray-900 mr-4 text-base">
                            Batal
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm text-base transition focus:ring-2 focus:ring-indigo-500">
                            <i data-feather="save" class="w-5 h-5 mr-2"></i>
                            Simpan Atasan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
