<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Edit Data Karyawan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl sm:rounded-xl border border-gray-200">

                {{-- HEADER --}}
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center space-x-3 rounded-t-xl">
                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                        <i data-feather="edit-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Edit Data Karyawan</h3>
                        <p class="text-sm text-gray-500">Perbarui informasi karyawan di sistem.</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.pegawai.update', $data->id) }}"
                      method="POST" enctype="multipart/form-data"
                      class="p-6 space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- SECTION 1 — Data Karyawan --}}
                    <div>
                        <h3 class="text-lg font-bold flex items-center text-gray-900 space-x-2">
                            <i data-feather="briefcase" class="w-5 h-5 text-indigo-600"></i>
                            <span>Data Karyawan</span>
                        </h3>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Nama --}}
                            <div class="md:col-span-2">
                                <label class="font-medium text-gray-700">Nama <span class="text-red-600">*</span></label>
                                <input type="text" name="nama" required
                                       value="{{ old('nama', $data->user->name) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            {{-- NIK Karyawan --}}
                            <div>
                                <label class="font-medium text-gray-700">NIK Karyawan <span class="text-red-600">*</span></label>
                                <input type="text" name="nik_karyawan" required
                                       value="{{ old('nik_karyawan', $data->nik_karyawan ?? $data->nip ?? $data->user->nik) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div>
                                <label class="font-medium text-gray-700">Jenis Kelamin <span class="text-red-600">*</span></label>
                                <select name="jenis_kelamin" required
                                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki" @selected(old('jenis_kelamin', $data->user->jenis_kelamin)=='Laki-laki')>Laki-laki</option>
                                    <option value="Perempuan" @selected(old('jenis_kelamin', $data->user->jenis_kelamin)=='Perempuan')>Perempuan</option>
                                </select>
                            </div>

                            {{-- Departemen --}}
                            <div>
                                <label class="font-medium text-gray-700">Departemen <span class="text-red-600">*</span></label>
                                <input type="text" name="departemen" required
                                       value="{{ old('departemen', $data->departemen) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            {{-- Divisi --}}
                            <div>
                                <label class="font-medium text-gray-700">Divisi <span class="text-red-600">*</span></label>
                                <input type="text" name="divisi" required
                                       value="{{ old('divisi', $data->divisi) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            {{-- Jabatan --}}
                            <div>
                                <label class="font-medium text-gray-700">Jabatan <span class="text-red-600">*</span></label>
                                <input type="text" name="jabatan" required
                                       value="{{ old('jabatan', $data->jabatan) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="font-medium text-gray-700">Status <span class="text-red-600">*</span></label>
                                <select name="status" required
                                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="Masih Bekerja" @selected(in_array(old('status', $data->status), ['Masih Bekerja', 'Aktif', null]))>Masih Bekerja</option>
                                    <option value="Tidak Aktif" @selected(old('status', $data->status)=='Tidak Aktif')>Tidak Aktif</option>
                                </select>
                            </div>

                            {{-- Foto Profil --}}
                            <div class="md:col-span-2">
                                <label class="font-medium text-gray-700">Foto Profil (Opsional)</label>
                                <div class="mt-2 flex items-center">
                                    @if ($data->user->profile_photo)
                                        <img id="previewImage" src="{{ asset('storage/' . $data->user->profile_photo) }}"
                                             class="w-20 h-20 rounded-lg object-cover mr-4 border border-gray-300">
                                    @else
                                        <img id="previewImage" src=""
                                             class="hidden w-20 h-20 rounded-lg object-cover mr-4 border border-gray-300">
                                    @endif

                                    <label class="cursor-pointer bg-blue-50 text-blue-700 border border-blue-300 hover:bg-blue-100 transition px-4 py-2 rounded-lg flex items-center gap-2 font-medium shadow-sm">
                                        <i data-feather="upload" class="w-5 h-5"></i>
                                        <span>Pilih Foto Baru</span>
                                        <input type="file" name="profile_photo" id="profile_photo"
                                            class="hidden" accept="image/*"
                                            onchange="previewFile(event)">
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- SECTION 2 — Akun Login --}}
                    <div class="pt-6 border-t border-gray-100">
                        <h3 class="text-lg font-bold flex items-center text-gray-900 space-x-2">
                            <i data-feather="lock" class="w-5 h-5 text-indigo-600"></i>
                            <span>Akun Login Karyawan</span>
                        </h3>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="font-medium text-gray-700">Email <span class="text-red-600">*</span></label>
                                <input type="email" name="email" required
                                       value="{{ old('email', $data->user->email) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <div>
                                <label class="font-medium text-gray-700">Password Baru (Opsional)</label>
                                <input type="password" name="password"
                                       placeholder="Kosongkan jika tidak diganti"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>

                    {{-- FOOTER --}}
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.pegawai.index') }}" 
                           class="text-gray-600 hover:text-gray-900 mr-4 text-base">
                            Batal
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm text-base transition focus:ring-2 focus:ring-indigo-500">
                            <i data-feather="save" class="w-5 h-5 mr-2"></i>
                            Perbarui Karyawan
                        </button>
                    </div>

                </form>

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
</x-app-layout>
