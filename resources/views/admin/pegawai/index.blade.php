<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Pegawai
        </h2>
    </x-slot>

    <div class="py-8"> {{-- Mengurangi padding vertical halaman --}}
        <div class="mx-auto sm:px-6 lg:px-8">
            
            {{-- Tabs Navigation --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <div class="border-b border-gray-200 mb-4 sm:mb-0 w-full sm:w-auto">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        {{-- Pegawai - Aktif --}}
                        <a href="{{ route('admin.pegawai.index') }}"
                           class="border-indigo-500 text-indigo-600 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center transition-colors">
                            <i data-feather="users" class="w-4 h-4 mr-2"></i>
                            Daftar Pegawai
                        </a>
                        {{-- Atasan --}}
                        <a href="{{ route('admin.atasan.index') }}"
                           class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center transition-colors">
                            <i data-feather="user-check" class="w-4 h-4 mr-2"></i>
                            Daftar Atasan
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Success Alert --}}
            @if (session('success'))
                <div class="mb-4 p-3 flex items-center bg-green-50 text-green-700 border border-green-200 rounded-lg text-sm shadow-sm">
                    <i data-feather="check-circle" class="w-4 h-4 mr-2 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Main Container --}}
           {{-- MAIN CONTENT --}}
<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg min-h-[500px]">

    {{-- HEADER SECTION (Sama gaya seperti Atasan) --}}
    <div class="px-6 py-5 border-b bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-600 text-white rounded-lg shadow-sm">
                <i data-feather="users" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Data Pegawai Non-PNS</h3>
                <p class="text-xs text-gray-500">
                    Kelola data pegawai beserta informasi penempatan & atasan langsung.
                </p>
            </div>
        </div>

        <a href="{{ route('admin.pegawai.create') }}"
           class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition text-sm font-medium">
            <i data-feather="plus" class="w-4 h-4 mr-1.5"></i>
            Tambah Pegawai
        </a>
    </div>

    {{-- CONTENT GRID --}}
    <div class="p-6 bg-gray-50/30">

        @if($data->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="bg-gray-100 p-4 rounded-full mb-3">
                    <i data-feather="users" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-gray-900 font-medium">Belum ada data pegawai</h3>
                <p class="text-gray-500 text-sm mt-1">Silakan tambahkan pegawai terlebih dahulu.</p>
            </div>
        @else

        {{-- GRID: 1 (HP) • 2 (Tablet) • 3 (Laptop) • 4 (XL) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

            @foreach ($data as $row)
            <div class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden flex flex-col">

                {{-- Decorative Gradient Header --}}
                <div class="h-20 bg-gradient-to-r from-indigo-500 to-purple-600"></div>

                {{-- CONTENT --}}
                <div class="px-5 pb-5 flex-1 flex flex-col">

                    {{-- Foto Pegawai (Overlapping) --}}
                    <div class="-mt-10 mb-3 flex justify-between items-end">
                        <div class="relative">
                            @if ($row->user->profile_photo)
                                <img src="{{ asset('storage/' . $row->user->profile_photo) }}"
                                     class="w-20 h-20 rounded-xl object-cover border-4 border-white shadow-sm">
                            @else
                                <div class="w-20 h-20 rounded-xl bg-white flex items-center justify-center border-4 border-white shadow-sm">
                                    <div class="w-full h-full bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-400">
                                        <i data-feather="user" class="w-8 h-8"></i>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Badge NIP --}}
                        <span class="mb-1 px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-mono rounded border border-gray-200">
                            {{ $row->nip ?? 'No NIP' }}
                        </span>
                    </div>

                    {{-- Nama + Jabatan --}}
                    <div class="mb-4">
                        <h3 class="text-base font-bold text-gray-900 line-clamp-1" title="{{ $row->user->name }}">
                            {{ $row->user->name }}
                        </h3>
                        <div class="flex items-center mt-1 text-indigo-600 text-xs font-medium uppercase tracking-wide">
                            {{ $row->jabatan ?? 'Jabatan Tidak Tersedia' }}
                        </div>
                    </div>

                    {{-- Detail List --}}
                    <div class="space-y-2 text-sm text-gray-600 flex-1">

                        {{-- Bidang --}}
                        <div class="flex items-start p-2 bg-gray-50 rounded-lg">
                            <i data-feather="layers" class="w-4 h-4 text-gray-400 mr-2 mt-0.5"></i>
                            <span class="font-medium text-gray-700 text-xs">
                                {{ $row->bidang->nama_bidang ?? '-' }}
                            </span>
                        </div>

                        {{-- Email --}}
                        <div class="flex items-center px-2">
                            <i data-feather="mail" class="w-3.5 h-3.5 text-gray-400 mr-2"></i>
                            <span class="truncate text-xs">{{ $row->user->email }}</span>
                        </div>

                        {{-- Atasan --}}
                        <div class="flex items-center px-2">
                            <i data-feather="user-check" class="w-3.5 h-3.5 text-gray-400 mr-2"></i>
                            <span class="truncate text-xs text-gray-700">
                                {{ $row->atasan->name ?? 'Tidak Ada Atasan' }}
                            </span>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="mt-5 pt-4 border-t border-gray-100 grid grid-cols-3 gap-3">

                        {{-- Lihat --}}
                        <a href="{{ route('admin.pegawai.show', $row->id) }}"
                           class="flex items-center justify-center px-3 py-2 bg-indigo-50 text-indigo-700 text-xs font-medium rounded hover:bg-indigo-100 transition border border-indigo-200/50">
                            <i data-feather="eye" class="w-3.5 h-3.5 mr-1.5"></i>
                            Detail
                        </a>

                        {{-- Edit --}}
                        <a href="{{ route('admin.pegawai.edit', $row->id) }}"
                           class="flex items-center justify-center px-3 py-2 bg-amber-50 text-amber-700 text-xs font-medium rounded hover:bg-amber-100 transition border border-amber-200/50">
                            <i data-feather="edit-2" class="w-3.5 h-3.5 mr-1.5"></i>
                            Edit
                        </a>

                        {{-- Delete --}}
                        <form action="{{ route('admin.pegawai.destroy', $row->id) }}" method="POST"
                              onsubmit="return confirm('Hapus pegawai ini?')">
                            @csrf
                            @method('DELETE')
                            <button
                                class="w-full flex items-center justify-center px-3 py-2 bg-white text-red-600 text-xs font-medium rounded hover:bg-red-50 transition border border-red-200">
                                <i data-feather="trash-2" class="w-3.5 h-3.5 mr-1.5"></i>
                                Hapus
                            </button>
                        </form>

                    </div>

                </div>
            </div>
            @endforeach

        </div>

        {{-- Pagination --}}
        <div class="mt-6 border-t border-gray-200 pt-4">
            {{ $data->links() }}
        </div>

        @endif

    </div>
</div>

        </div>
    </div>
</x-app-layout>