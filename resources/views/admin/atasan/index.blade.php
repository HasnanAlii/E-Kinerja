<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Atasan
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto sm:px-6 lg:px-8">
            
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <div class="border-b border-gray-200 mb-4 sm:mb-0 w-full sm:w-auto">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="{{ route('admin.pegawai.index') }}"
                           class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center transition-colors">
                            <i data-feather="users" class="w-4 h-4 mr-2"></i>
                            Daftar Pegawai
                        </a>
                        <a href="{{ route('admin.atasan.index') }}"
                           class="border-indigo-500 text-indigo-600 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center transition-colors">
                            <i data-feather="user-check" class="w-4 h-4 mr-2"></i>
                            Daftar Atasan
                        </a>
                    </nav>
                </div>
            </div>
            
            @if (session('success'))
                <div class="mb-6 p-4 flex items-center bg-green-50 text-green-700 border border-green-200 rounded-lg text-sm shadow-sm">
                    <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg min-h-[500px]">
                
    {{-- HEADER SECTION --}}
<div class="px-6 py-6 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    
    {{-- BAGIAN KIRI: ICON & JUDUL --}}
    <div class="flex items-center gap-4">
        <div class="p-3 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-200">
            <i data-feather="briefcase" class="w-6 h-6"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-900 tracking-tight">
                Data Pimpinan & Atasan
            </h3>
            <p class="text-sm text-gray-500 mt-0.5">
                Kelola data struktural dan pimpinan unit.
            </p>
        </div>
    </div>

    {{-- BAGIAN KANAN: FILTER & TOMBOL --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">

        {{-- FILTER (Baru Ditambahkan) --}}
        <form method="GET" class="w-full sm:w-auto">
            <div class="relative">
                {{-- Icon Filter Kiri --}}
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i data-feather="filter" class="w-4 h-4"></i>
                </div>

                <select name="bidang_id" onchange="this.form.submit()"
                    class="w-full sm:w-56 pl-10 pr-10 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 cursor-pointer appearance-none hover:bg-gray-50 shadow-sm">
                    <option value="">Semua Bidang</option>
                    {{-- Pastikan variabel $bidang dikirim dari Controller --}}
                    @foreach($bidang as $b)
                        <option value="{{ $b->id }}" {{ request('bidang_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_bidang }}
                        </option>
                    @endforeach
                </select>

                {{-- Icon Panah Kanan --}}
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                    <i data-feather="chevron-down" class="w-4 h-4"></i>
                </div>
            </div>
        </form>

            <a href="{{ route('admin.atasan.create') }}"
                class="inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-2.5 rounded-xl shadow-md shadow-indigo-200 hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-300 transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 font-medium text-sm">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                Tambah Atasan
            </a>

 

    </div>
</div>

                <div class="p-6 bg-gray-50/30">
                    
                    @if($atasan->isEmpty())
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="bg-gray-100 p-4 rounded-full mb-3">
                                <i data-feather="user-x" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <h3 class="text-gray-900 font-medium">Belum ada data atasan</h3>
                            <p class="text-gray-500 text-sm mt-1">Silakan tambahkan data atasan baru.</p>
                        </div>
                    @else

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                        @foreach ($atasan as $a)
                        <div class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden flex flex-col">
                            
                            <div class="h-20 bg-gradient-to-r from-indigo-500 to-purple-600"></div>

                            <div class="px-5 pb-5 flex-1 flex flex-col">
                                
                                <div class="-mt-10 mb-3 flex justify-between items-end">
                                    <div class="relative">
                                        @if ($a->user->profile_photo)
                                            <img src="{{ asset('storage/' . $a->user->profile_photo) }}"
                                                 class="w-20 h-20 rounded-xl object-cover border-4 border-white shadow-sm">
                                        @else
                                            <div class="w-20 h-20 rounded-xl bg-white flex items-center justify-center border-4 border-white shadow-sm">
                                                <div class="w-full h-full bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-400">
                                                    <i data-feather="user" class="w-8 h-8"></i>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <span class="mb-1 px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-mono rounded border border-gray-200">
                                        {{ $a->nip ?? 'No NIP' }}
                                    </span>
                                </div>

                                <div class="mb-4">
                                    <h3 class="text-base font-bold text-gray-900 line-clamp-1" title="{{ $a->user->name }}">
                                        {{ $a->user->name }}
                                    </h3>
                                    <div class="flex items-center mt-1 text-indigo-600 text-xs font-medium uppercase tracking-wide">
                                        {{ $a->jabatan ?? 'Jabatan Kosong' }}
                                    </div>
                                </div>

                                <div class="space-y-2 text-sm text-gray-600 flex-1">
                                    <div class="flex items-start p-2 bg-gray-50 rounded-lg">
                                        <i data-feather="layers" class="w-4 h-4 text-gray-400 mr-2 mt-0.5"></i>
                                        <span class="font-medium text-gray-700 text-xs">
                                            {{ $a->bidang->nama_bidang ?? '-' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center px-2">
                                        <i data-feather="mail" class="w-3.5 h-3.5 text-gray-400 mr-2"></i>
                                        <span class="truncate text-xs">{{ $a->user->email }}</span>
                                    </div>
                                </div>

                                <div class="mt-5 pt-4 border-t border-gray-100 grid grid-cols-3 gap-3">
                                         <a href="{{ route('admin.atasan.show', $a->id) }}"
                                    class="flex items-center justify-center px-3 py-2 bg-indigo-50 text-indigo-700 text-xs font-medium rounded hover:bg-indigo-100 transition border border-indigo-200/50">
                                        <i data-feather="eye" class="w-3.5 h-3.5 mr-1.5"></i>
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.atasan.edit', $a->id) }}"
                                       class="flex items-center justify-center px-3 py-2 bg-amber-50 text-amber-700 text-xs font-medium rounded hover:bg-amber-100 transition border border-amber-200/50">
                                        <i data-feather="edit-2" class="w-3.5 h-3.5 mr-1.5"></i> Edit
                                    </a>

                                    <form action="{{ route('admin.atasan.destroy', $a->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus data atasan ini? Data pegawai bawahan mungkin akan terpengaruh.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full flex items-center justify-center px-3 py-2 bg-white text-red-600 text-xs font-medium rounded hover:bg-red-50 transition border border-red-200">
                                            <i data-feather="trash-2" class="w-3.5 h-3.5 mr-1.5"></i> Hapus
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                        @endforeach

                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>