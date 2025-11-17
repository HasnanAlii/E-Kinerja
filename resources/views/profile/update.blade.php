<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Profil Saya
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- FORM KIRIM ULANG VERIFIKASI --}}
            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            {{-- CARD --}}
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <form method="post" action="{{ route('profil.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    {{-- ========================== --}}
                    {{--        HEADER SECTION       --}}
                    {{-- ========================== --}}
                    <header class="p-6 border-b border-gray-200 flex items-start gap-3">
                        <span class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                            <i data-feather="user" class="w-6 h-6"></i>
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-blue-900">
                                Informasi Profil
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Perbarui data profil Anda sesuai kebutuhan.
                            </p>
                        </div>
                    </header>

                    <div class="p-6 space-y-10">

                        {{-- ========================== --}}
                        {{--         FOTO PROFIL        --}}
                        {{-- ========================== --}}
                        <x-profile.input-row id="profile_photo" label="Foto Profil">
                            <div class="flex items-center gap-6">

                                {{-- Foto Saat Ini --}}
                                @if ($user->profile_photo)
                                    <img src="{{ asset('storage/' . $user->profile_photo) }}"
                                        class="w-24 h-24 object-cover rounded-full border-4 border-blue-100 shadow-sm">
                                @else
                                    <div class="w-24 h-24 rounded-full bg-blue-50 flex items-center justify-center border border-blue-200">
                                        <i data-feather="user" class="w-10 h-10 text-blue-400"></i>
                                    </div>
                                @endif

                                {{-- Upload Baru --}}
                                <div>
                                    <label for="profile_photo" class="cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-700">
                                        Ganti Foto
                                        <input id="profile_photo" name="profile_photo" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG/PNG, Max 12MB.</p>
                                </div>
                            </div>
                        </x-profile.input-row>

                        {{-- ========================== --}}
                        {{--         DATA USER          --}}
                        {{-- ========================== --}}

                        <x-profile.section title="Data Akun" desc="Informasi akun dasar Anda.">

                            <x-profile.input-row id="name" label="Nama Lengkap">
                                <x-text-input name="name" type="text" class="w-full"
                                    :value="old('name', $user->name)" required />
                            </x-profile.input-row>

                            <x-profile.input-row id="email" label="Alamat Email">
                                <x-text-input name="email" type="email" class="w-full"
                                    :value="old('email', $user->email)" required />

                                {{-- STATUS VERIFIKASI EMAIL --}}
                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="mt-3 bg-yellow-50 border border-yellow-200 p-4 rounded-md">
                                        <p class="text-sm text-yellow-800">Email Anda belum diverifikasi.</p>
                                        <button form="send-verification" class="underline text-sm text-yellow-600 hover:text-yellow-700">
                                            Kirim ulang tautan verifikasi →
                                        </button>

                                        @if (session('status') === 'verification-link-sent')
                                            <p class="text-sm mt-2 text-green-600">Tautan verifikasi telah dikirim.</p>
                                        @endif
                                    </div>
                                @endif
                            </x-profile.input-row>

                            <x-profile.input-row id="nik" label="NIK">
                                <x-text-input name="nik" type="text" class="w-full"
                                    :value="old('nik', $user->nik)" />
                            </x-profile.input-row>

                            <x-profile.input-row id="jenis_kelamin" label="Jenis Kelamin">
                                <select name="jenis_kelamin" class="w-full border rounded-lg py-2 px-3">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </x-profile.input-row>

                            <x-profile.input-row id="tanggal_lahir" label="Tanggal Lahir">
                                <x-text-input name="tanggal_lahir" type="date" class="w-full"
                                    :value="old('tanggal_lahir', $user->tanggal_lahir)" />
                            </x-profile.input-row>

                            <x-profile.input-row id="tempat_lahir" label="Tempat Lahir">
                                <x-text-input name="tempat_lahir" type="text" class="w-full"
                                    :value="old('tempat_lahir', $user->tempat_lahir)" />
                            </x-profile.input-row>

                            <x-profile.input-row id="agama" label="Agama">
                                <x-text-input name="agama" type="text" class="w-full"
                                    :value="old('agama', $user->agama)" />
                            </x-profile.input-row>

                            <x-profile.input-row id="alamat" label="Alamat">
                                <textarea name="alamat" class="w-full border rounded-lg p-2" rows="2">{{ old('alamat', $user->alamat) }}</textarea>
                            </x-profile.input-row>

                            <x-profile.input-row id="telp" label="No. Telpon">
                                <x-text-input name="telp" type="text" class="w-full"
                                    :value="old('telp', $user->telp)" />
                            </x-profile.input-row>

                        </x-profile.section>

                        {{-- ========================== --}}
                        {{--         DATA PEGAWAI       --}}
                        {{-- ========================== --}}
                        @if ($pegawai)
                            <x-profile.section title="Data Pegawai" desc="Informasi kepegawaian Anda.">

                                <x-profile.input-row id="pegawai_nip" label="NIP">
                                    <x-text-input name="nip" type="text" class="w-full"
                                        :value="old('nip', $pegawai->nip)" />
                                </x-profile.input-row>

                                <x-profile.input-row id="pegawai_jabatan" label="Jabatan">
                                    <x-text-input name="jabatan" type="text" class="w-full"
                                        :value="old('jabatan', $pegawai->jabatan)" />
                                </x-profile.input-row>

                                <x-profile.input-row id="pegawai_bidang" label="Bidang">
                                    <x-text-input disabled class="w-full bg-gray-100"
                                        value="{{ $pegawai->bidang->nama_bidang ?? '-' }}" />
                                </x-profile.input-row>

                                <x-profile.input-row id="pegawai_atasan" label="Atasan">
                                    <x-text-input disabled class="w-full bg-gray-100"
                                        value="{{ $pegawai->atasan->name ?? '-' }}" />
                                </x-profile.input-row>

                                <x-profile.input-row id="pegawai_masa_kontrak" label="Masa Kontrak">
                                    <x-text-input name="masa_kontrak" type="date" class="w-full"
                                        :value="old('masa_kontrak', $pegawai->masa_kontrak)" />
                                </x-profile.input-row>

                                <x-profile.input-row id="pegawai_status" label="Status Pegawai">
                                    <select name="status" class="w-full border rounded-lg py-2 px-3">
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Aktif" {{ old('status', $pegawai->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Tidak Aktif" {{ old('status', $pegawai->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </x-profile.input-row>

                                <x-profile.input-row id="pegawai_tanggal_masuk" label="Tanggal Masuk">
                                    <x-text-input name="tanggal_masuk" type="date" class="w-full"
                                        :value="old('tanggal_masuk', $pegawai->tanggal_masuk)" />
                                </x-profile.input-row>

                            </x-profile.section>
                        @endif

                        {{-- ========================== --}}
                        {{--         DATA ATASAN        --}}
                        {{-- ========================== --}}
                        @if ($atasan)
                            <x-profile.section title="Data Atasan" desc="Informasi jabatan Anda sebagai Atasan.">

                                <x-profile.input-row id="atasan_nip" label="NIP">
                                    <x-text-input name="nip" type="text" class="w-full"
                                        :value="old('nip', $atasan->nip)" />
                                </x-profile.input-row>

                                <x-profile.input-row id="atasan_jabatan" label="Jabatan">
                                    <x-text-input name="jabatan" type="text" class="w-full"
                                        :value="old('jabatan', $atasan->jabatan)" />
                                </x-profile.input-row>

                                <x-profile.input-row id="atasan_golongan" label="Golongan">
                                    <x-text-input name="golongan" type="text" class="w-full"
                                        :value="old('golongan', $atasan->golongan)" />
                                </x-profile.input-row>


                                <x-profile.input-row id="atasan_masa_kontrak" label="Masa Kontrak">
                                    <x-text-input name="masa_kontrak" type="date" class="w-full"
                                        :value="old('masa_kontrak', $atasan->masa_kontrak)" />
                                </x-profile.input-row>

                            <x-profile.input-row id="atasan_status" label="Status">

                            <select name="status" disabled
                                class="w-full border rounded-lg py-2 px-3 bg-gray-100 text-gray-500 cursor-not-allowed">
                                
                                <option value="">-- Pilih Status --</option>
                                <option value="Aktif" {{ old('status', $atasan->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ old('status', $atasan->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>

                        </x-profile.input-row>


                                <x-profile.input-row id="atasan_tanggal_masuk" label="Tanggal Masuk">
                                    <x-text-input name="tanggal_masuk" type="date" class="w-full"
                                        :value="old('tanggal_masuk', $atasan->tanggal_masuk)" />
                                </x-profile.input-row>

                            </x-profile.section>
                        @endif

                    </div>

                    {{-- ========================== --}}
                    {{--          FOOTER BTN        --}}
                    {{-- ========================== --}}
                    <footer class="flex justify-end items-center gap-4 bg-gray-50 p-6 border-t border-gray-200">

                        @if (session('status') === 'profile-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition
                                x-init="setTimeout(() => show = false, 3000)"
                                class="text-sm text-green-600">
                                Profil berhasil diperbarui.
                            </p>
                        @endif

                        <x-primary-button class="flex items-center gap-2 bg-blue-500 hover:bg-blue-700">
                            <i data-feather="save" class="w-5 h-5"></i>
                            Simpan Perubahan
                        </x-primary-button>

                    </footer>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        if (typeof feather !== 'undefined') feather.replace();
    });
</script>
