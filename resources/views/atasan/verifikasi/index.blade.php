<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold text-gray-800">
            Verifikasi Aktivitas Pegawai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">

            {{-- CARD WRAPPER --}}
            <div class=" shadow-xl sm:rounded-2xl border border-gray-200 overflow-hidden">

                {{-- 1. HEADER SECTION --}}
                <div class="px-6 py-6 border-b border-gray-200 flex items-center justify-between bg-gray-50 ">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl shadow-sm ring-1 ring-indigo-100">
                            <i data-feather="clipboard" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Daftar Aktivitas Menunggu</h3>
                            <p class="text-sm text-gray-500 mt-0.5">
                                Aktivitas harian pegawai yang perlu Anda verifikasi.
                            </p>
                        </div>
                    </div>
                    
                    {{-- 2. FILTER SECTION (Sesuai request) --}}
                        <form method="GET" class="flex flex-col xl:flex-row gap-4">
                            
                            {{-- Filter Pegawai --}}
                            <div class="relative w-full xl:w-64">
                                <label for="pegawai_id" class="sr-only">Pegawai</label>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i data-feather="user" class="w-4 h-4"></i>
                                </div>
                                <select name="pegawai_id"
                                class="w-full pl-10 pr-8 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 cursor-pointer hover:bg-gray-50">
                                <option value="">Semua Pegawai</option>
                                {{-- Pastikan variabel $pegawais dikirim dari controller --}}
                                @foreach($pegawais ?? [] as $p)
                                <option value="{{ $p->id }}" @selected(request('pegawai_id') == $p->id)>
                                    {{ $p->user->name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i data-feather="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                        
                        {{-- Filter Tanggal (Grouped) --}}
                        <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-xl p-1 shadow-sm w-full xl:w-auto hover:border-indigo-400 transition-colors">
                            {{-- Tanggal Dari --}}
                            <div class="relative flex-1">
                                <input type="date" name="tanggal_dari" 
                                value="{{ request('tanggal_dari') }}"
                                class="w-full py-1.5 pl-3 pr-1 bg-transparent border-none text-gray-700 text-sm focus:ring-0 cursor-pointer"
                                placeholder="Dari">
                            </div>
                            
                            <span class="text-gray-400 text-xs font-medium px-1">s/d</span>
                            
                            {{-- Tanggal Sampai --}}
                            <div class="relative flex-1">
                                <input type="date" name="tanggal_sampai" 
                                value="{{ request('tanggal_sampai') }}"
                                class="w-full py-1.5 pl-1 pr-3 bg-transparent border-none text-gray-700 text-sm focus:ring-0 cursor-pointer text-right"
                                placeholder="Sampai">
                            </div>
                        </div>
                        
                        {{-- Tombol Filter --}}
                        <button type="submit"
                        class="inline-flex items-center justify-center bg-indigo-600 text-white px-6 py-2.5 rounded-xl shadow-md shadow-indigo-200 hover:bg-indigo-700 transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 xl:w-auto w-full">
                        <i data-feather="filter" class="w-4 h-4 mr-2"></i>
                        <span class="font-medium text-sm">Terapkan</span>
                    </button>
                </form>
        </div>

                      {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-500 uppercase">Pegawai</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-500 uppercase">Aktivitas</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($data as $row)
                                <tr class="hover:bg-indigo-50/30 transition-colors">

                                    {{-- PEGAWAI --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($row->pegawai->user->profile_photo)
                                                <img src="{{ asset('storage/' . $row->pegawai->user->profile_photo) }}"
                                                    class="h-11 w-11 rounded-full object-cover border shadow-sm">
                                            @else
                                                <div class="h-11 w-11 rounded-full bg-indigo-50 flex items-center justify-center border border-indigo-100 text-indigo-400">
                                                    <i data-feather="user" class="w-5 h-5"></i>
                                                </div>
                                            @endif

                                            <div>
                                                <span class="text-base font-semibold text-gray-900">
                                                    {{ $row->pegawai->user->name }}
                                                </span>
                                                <span class="text-sm text-gray-500 block">
                                                    {{ $row->pegawai->jabatan ?? 'Pegawai' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- TANGGAL --}}
                                    <td class="px-6 py-4 text-base text-gray-700">
                                        <div class="flex items-center">
                                            <i data-feather="calendar" class="w-4 h-4 mr-2 text-gray-400"></i>
                                            {{ \Carbon\Carbon::parse($row->tanggal)->isoFormat('D MMM YYYY') }}
                                        </div>
                                    </td>

                                    {{-- AKTIVITAS --}}
                                    <td class="px-6 py-4 text-base text-gray-700">
                                        <p class="line-clamp-2 max-w-xs">
                                            {{ $row->uraian_tugas }}
                                        </p>
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="px-6 py-4 text-center">
                                        @if ($row->status === 'menunggu')
                                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                ● Menunggu
                                            </span>
                                        @elseif ($row->status === 'disetujui')
                                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full">
                                                ● Disetujui
                                            </span>
                                        @elseif ($row->status === 'ditolak')
                                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-red-100 text-red-800 rounded-full">
                                                ● Ditolak
                                            </span>
                                        @elseif ($row->status === 'revisi')
                                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">
                                                ● Revisi
                                            </span>
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('atasan.verifikasi.show', $row->id) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-200 rounded-lg text-gray-700 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition-all">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-base text-gray-500">
                                        Tidak ada aktivitas yang perlu diverifikasi.<br>
                                        <span class="text-sm text-gray-400">Coba ubah filter tanggal atau pegawai.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if ($data->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $data->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
</x-app-layout>
