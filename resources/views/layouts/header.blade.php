{{--  require alpine.js if not included --}}
<script src="https://unpkg.com/alpinejs" defer></script>

<nav class="bg-white border-b shadow sticky top-0 z-50">
    <div class="px-10 mx-auto py-3 flex justify-between items-center">

        {{-- TITLE --}}
        <div class="text-xl font-bold text-gray-700">
            {{ $title ?? 'Dashboard' }}
        </div>

        <div class="flex items-center gap-6">

            {{-- ROLE BADGE --}}
            <span class="px-3 py-1 text-xs font-semibold rounded-full
                @if(Auth::user()->hasRole('admin'))
                    bg-red-100 text-red-700
                @elseif(Auth::user()->hasRole('atasan'))
                    bg-indigo-100 text-indigo-700
                @else
                    bg-green-100 text-green-700
                @endif
            ">
                {{ ucfirst(Auth::user()->roles->first()->name ?? 'User') }}
            </span>

            {{-- NOTIFICATION --}}
            <div x-data="{ openNotif: false }" class="relative">
                
                <button @click="openNotif = !openNotif"
                    class="relative focus:outline-none">
                    <i data-feather="bell" class="w-6 h-6 text-gray-600"></i>
                    
                    {{-- NOTIF BADGE --}}
                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1 rounded-full">
                        3
                    </span>
                </button>

                {{-- DROPDOWN NOTIF --}}
                <div x-show="openNotif"
                     @click.outside="openNotif = false"
                     x-transition
                     class="absolute right-0 mt-3 w-72 bg-white shadow-lg rounded-lg border border-gray-200 z-50">
                    
                    <div class="p-3 border-b font-semibold">Notifikasi</div>

                    <div class="max-h-64 overflow-y-auto">

                        <a href="#" class="block px-4 py-3 hover:bg-gray-50">
                            <p class="text-sm text-gray-800 font-medium">
                                Pengajuan cuti menunggu persetujuan
                            </p>
                            <p class="text-xs text-gray-500">2 jam lalu</p>
                        </a>

                        <a href="#" class="block px-4 py-3 hover:bg-gray-50">
                            <p class="text-sm text-gray-800 font-medium">
                                Laporan kinerja berhasil diunggah
                            </p>
                            <p class="text-xs text-gray-500">Kemarin</p>
                        </a>

                    </div>

                    <div class="p-2 border-t text-center">
                        <a href="#" class="text-indigo-600 text-sm hover:underline">
                            Lihat semua notifikasi
                        </a>
                    </div>
                </div>
            </div>



            {{-- PROFILE DROPDOWN --}}
            <div class="relative" x-data="{ open: false }">

                <button @click="open = !open"
                        class="flex items-center gap-3 focus:outline-none">

                    {{-- FOTO PROFIL --}}
                    @if (Auth::user()->profile_photo ?? false)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}"
                             class="w-10 h-10 rounded-full object-cover border shadow">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-300 border flex items-center justify-center shadow">
                            <i data-feather="user" class="w-5 h-5 text-gray-600"></i>
                        </div>
                    @endif

                    {{-- NAMA --}}
                    <span class="text-gray-700 font-semibold">
                        {{ Auth::user()->name }}
                    </span>

                    <i data-feather="chevron-down" class="w-4 h-4 text-gray-600"></i>
                </button>

                {{-- DROPDOWN --}}
                <div x-show="open"
                     @click.outside="open = false"
                     x-transition
                     class="absolute right-0 mt-3 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 py-2">

                    {{-- Profil Saya --}}
                    <a href="{{ route('profil.edit') }}"
                       class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm">
                        <i data-feather="user" class="w-4 h-4"></i>
                        Profil Saya
                    </a>

                    {{-- Ubah Password --}}
                       <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm">
                        <i data-feather="key" class="w-4 h-4"></i>
                        Ubah Kata Sandi
                    </a>

                    <div class="border-t my-1"></div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="w-full flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 text-sm">
                            <i data-feather="log-out" class="w-4 h-4"></i>
                            Logout
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</nav>
