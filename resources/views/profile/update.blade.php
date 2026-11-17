<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Profil &amp; Data Diri
        </h2>
    </x-slot>

    <div class="py-6">
        <div class=" mx-auto sm:px-6 lg:px-8">

            {{-- FORM KIRIM ULANG VERIFIKASI --}}
            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="post" action="{{ route('profil.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('patch')

                {{-- HERO HEADER PROFIL --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 p-6 text-white shadow-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center shrink-0">
                            <i data-feather="user-check" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold tracking-tight">Kelola Profil &amp; Data Diri</h2>
                            <p class="text-xs text-indigo-100 mt-0.5">PT Pou Yuen Indonesia — Perbarui foto profil dan informasi identitas kerja Anda.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if (session('status') === 'profile-updated')
                            <span x-data="{ show: true }" x-show="show" x-transition
                                  x-init="setTimeout(() => show = false, 3000)"
                                  class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-500 text-white text-xs font-bold shadow-xs">
                                <i data-feather="check-circle" class="w-4 h-4"></i>
                                <span>Profil berhasil diperbarui</span>
                            </span>
                        @endif

                        {{-- <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-indigo-700 hover:bg-indigo-50 text-sm font-bold rounded-xl shadow-md transition">
                            <i data-feather="save" class="w-4 h-4"></i>
                            <span>Simpan Perubahan</span>
                        </button> --}}
                    </div>
                </div>

                {{-- TATA LETAK HORIZONTAL (KIRI: FOTO PROFIL, KANAN: FORM GRID 2 KOLOM) --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                    {{-- KOLOM KIRI (4 KOLOM): KARTU FOTO PROFIL & RINGKASAN --}}
                    <div class="lg:col-span-4 bg-white rounded-2xl shadow-sm border border-slate-200/90 p-6 text-center">
                        <div class="relative inline-block">
                            @if ($user->profile_photo)
                                <img src="{{ asset('storage/' . $user->profile_photo) }}"
                                     class="w-32 h-32 object-cover rounded-2xl border-4 border-indigo-50 shadow-md mx-auto">
                            @else
                                <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white font-extrabold text-3xl flex items-center justify-center border-4 border-indigo-50 shadow-md mx-auto">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                                </div>
                            @endif
                        </div>

                        <h3 class="mt-4 text-lg font-extrabold text-slate-800">{{ $user->name }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $user->email }}</p>

                        <div class="mt-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $pegawai->status ?? ($atasan->status ?? 'Masih Bekerja') }}
                            </span>
                        </div>

                        <div class="mt-5 pt-5 border-t border-slate-100">
                            <label for="profile_photo"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs border border-indigo-200/80 cursor-pointer transition">
                                <i data-feather="camera" class="w-4 h-4"></i>
                                <span>Pilih / Ganti Foto Profil</span>
                                <input id="profile_photo" name="profile_photo" type="file" class="sr-only" accept="image/*">
                            </label>
                            <p class="text-[11px] text-slate-400 mt-2">Format: JPG, PNG (Maks. 2MB)</p>
                        </div>
                    </div>

                    {{-- KOLOM KANAN (8 KOLOM): FORM INPUT HORIZONTAL --}}
                    <div class="lg:col-span-8 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden">
                            <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <i data-feather="briefcase" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold text-slate-800">Data Diri &amp; Kepegawaian PT Pou Yuen</h3>
                                    <p class="text-xs text-slate-400">Susunan formulir horizontal untuk memudahkan pengisian data.</p>
                                </div>
                            </div>

                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                                {{-- Nama --}}
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Lengkap</label>
                                    <x-text-input name="name" type="text" class="w-full rounded-xl"
                                                  :value="old('name', $user->name)" required />
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Alamat Email</label>
                                    <x-text-input name="email" type="email" class="w-full rounded-xl"
                                                  :value="old('email', $user->email)" required />
                                </div>

                                {{-- Jenis Kelamin --}}
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm py-2 px-3 text-sm">
                                        <option value="">-- Pilih --</option>
                                        <option value="Laki-laki" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>

                                @if ($pegawai)
                                    {{-- NIK Karyawan --}}
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">NIK Karyawan</label>
                                        <x-text-input name="nik_karyawan" type="text" class="w-full rounded-xl font-mono"
                                                      :value="old('nik_karyawan', $pegawai->nik_karyawan ?? $pegawai->nip ?? $user->nik)" />
                                    </div>

                                    {{-- Departemen --}}
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Departemen</label>
                                        <x-text-input name="departemen" type="text" class="w-full rounded-xl"
                                                      :value="old('departemen', $pegawai->departemen)" />
                                    </div>

                                    {{-- Divisi --}}
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Divisi</label>
                                        <x-text-input name="divisi" type="text" class="w-full rounded-xl"
                                                      :value="old('divisi', $pegawai->divisi)" />
                                    </div>

                                    {{-- Jabatan --}}
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Jabatan</label>
                                        <x-text-input name="jabatan" type="text" class="w-full rounded-xl"
                                                      :value="old('jabatan', $pegawai->jabatan)" />
                                    </div>

                                    {{-- Status --}}
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Status Kerja</label>
                                        <x-text-input disabled class="w-full rounded-xl bg-slate-100 text-slate-600"
                                                      value="{{ $pegawai->status ?? 'Masih Bekerja' }}" />
                                    </div>
                                @else
                                    {{-- NIK --}}
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">NIK</label>
                                        <x-text-input name="nik" type="text" class="w-full rounded-xl font-mono"
                                                      :value="old('nik', $user->nik)" />
                                    </div>
                                @endif
                            </div>

                            @if ($atasan)
                                <div class="px-6 py-4 bg-slate-50/70 border-t border-b border-slate-100 flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                        <i data-feather="shield" class="w-4 h-4"></i>
                                    </div>
                                    <h3 class="text-sm font-extrabold text-slate-800">Informasi Jabatan Atasan</h3>
                                </div>

                                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">NIP Atasan</label>
                                        <x-text-input name="nip" type="text" class="w-full rounded-xl"
                                                      :value="old('nip', $atasan->nip)" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Jabatan</label>
                                        <x-text-input name="jabatan" type="text" class="w-full rounded-xl"
                                                      :value="old('jabatan', $atasan->jabatan)" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Golongan</label>
                                        <x-text-input name="golongan" type="text" class="w-full rounded-xl"
                                                      :value="old('golongan', $atasan->golongan)" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Status Kepegawaian</label>
                                        <x-text-input disabled class="w-full rounded-xl bg-slate-100 text-slate-600"
                                                      value="{{ $atasan->status ?? 'Aktif' }}" />
                                    </div>
                                </div>
                            @endif

                            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md shadow-indigo-500/20 transition">
                                    <i data-feather="save" class="w-4 h-4"></i>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
