<section class="bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden">
    {{-- HEADER GRADIEN --}}
    <header class="px-6 py-4 bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 text-white flex items-center justify-between">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-white/15 border border-white/25 flex items-center justify-center shrink-0">
                <i data-feather="user" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h2 class="text-lg font-extrabold tracking-tight">
                    Informasi Profil Akun
                </h2>
                <p class="text-xs text-indigo-100 mt-0.5">
                    Perbarui nama pengguna dan alamat email resmi akun Anda secara langsung.
                </p>
            </div>
        </div>
        <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/15 border border-white/20">
            PT Pou Yuen Indonesia
        </span>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="p-6">
        @csrf
        @method('patch')

        {{-- TATA LETAK HORIZONTAL (KIRI: RINGKASAN PROFIL, KANAN: INPUT HORIZONTAL) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">

            {{-- KIRI: KARTU AVATAR RINGKAS --}}
            <div class="lg:col-span-4 bg-gradient-to-br from-indigo-50/80 via-blue-50/50 to-white border border-indigo-100 rounded-2xl p-4 flex items-center gap-4">
                @if ($user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}"
                         class="w-16 h-16 rounded-2xl object-cover ring-2 ring-white shadow-md shrink-0">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white font-extrabold text-xl flex items-center justify-center shadow-md shrink-0">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-extrabold text-slate-800 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500 truncate mt-0.5">{{ $user->email }}</p>
                    <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Akun Aktif
                    </span>
                </div>
            </div>

            {{-- KANAN: FORM INPUT MENYAMPING (HORIZONTAL) --}}
            <div class="lg:col-span-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Input Nama --}}
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            Nama Lengkap
                        </label>
                        <div class="relative">
                            <i data-feather="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                            <input id="name" name="name" type="text"
                                   value="{{ old('name', $user->name) }}"
                                   required autofocus autocomplete="name"
                                   placeholder="Masukkan nama lengkap"
                                   class="pl-10 pr-3 py-2.5 block w-full rounded-xl border-slate-300 text-sm font-medium text-slate-800 shadow-2xs focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                    </div>

                    {{-- Input Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <i data-feather="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                            <input id="email" name="email" type="email"
                                   value="{{ old('email', $user->email) }}"
                                   required autocomplete="username"
                                   placeholder="nama@pouyuen.co.id"
                                   class="pl-10 pr-3 py-2.5 block w-full rounded-xl border-slate-300 text-sm font-medium text-slate-800 shadow-2xs focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2">
                                <p class="text-xs text-amber-700">
                                    Email belum diverifikasi.
                                    <button form="send-verification" class="underline font-semibold hover:text-amber-900">
                                        Kirim ulang link verifikasi.
                                    </button>
                                </p>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-1 font-semibold text-xs text-emerald-600">
                                        Link verifikasi baru telah dikirim ke email Anda.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Baris Tombol Simpan di Kanan Bawah --}}
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    @if (session('status') === 'profile-updated')
                        <p x-data="{ show: true }"
                           x-show="show"
                           x-transition
                           x-init="setTimeout(() => show = false, 2500)"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl">
                            <i data-feather="check-circle" class="w-4 h-4"></i>
                            <span>Perubahan tersimpan</span>
                        </p>
                    @endif

                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md shadow-indigo-500/20 transition">
                        <i data-feather="save" class="w-4 h-4"></i>
                        <span>Simpan Profil</span>
                    </button>
                </div>
            </div>

        </div>
    </form>
</section>
