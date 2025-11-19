<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Manajemen Kehadiran
        </h2>
    </x-slot>

    <div class="py-12 mx-auto sm:px-6 lg:px-8">
     
     {{-- <div class="mb-6">
         <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="{{ route('atasan.kehadiran.index') }}"
                           class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                            <i data-feather="clock" class="w-5 h-5 mr-2"></i>
                            Rekap Kehadiran
                        </a>

                        <a href="{{ route('atasan.izin.index') }}"
                           class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                            <i data-feather="file-text" class="w-5 h-5 mr-2"></i>
                            Izin / Sakit
                        </a>
                    </nav>
                </div>
            </div> --}}

            @if(session('success'))
                <div class="mb-4 p-4 flex items-center bg-green-100 text-green-700 rounded-lg border border-green-300 text-base">
                    <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            {{-- HEADER CARD --}}
        <div class="px-6 py-6 border-b border-gray-200 bg-gray-50 rounded-t-lg flex flex-col xl:flex-row xl:items-center justify-between gap-6">

            {{-- LEFT SECTION: ICON + TITLE --}}
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl shadow-sm ring-1 ring-indigo-100">
                    <i data-feather="clock" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Rekap Kehadiran</h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Data kehadiran berdasarkan pegawai & periode.
                    </p>
                </div>
            </div>

            {{-- RIGHT SECTION: FILTER FORM --}}
            <form method="GET" class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">

                {{-- Filter Bidang --}}
                <div class="relative w-full sm:w-48">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i data-feather="layers" class="w-4 h-4"></i>
                    </div>
                    <select name="bidang_id" onchange="this.form.submit()"
                        class="w-full pl-10 pr-8 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 cursor-pointer appearance-none hover:bg-gray-100">
                        <option value="">Semua Bidang</option>
                        @foreach($bidangs as $b)
                            <option value="{{ $b->id }}" @selected(request('bidang_id') == $b->id)>
                                {{ $b->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                        <i data-feather="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>

                {{-- Filter Tanggal (Group) --}}
                <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl p-1">
                    
                    {{-- Tanggal Dari --}}
                    <div class="relative flex-1 sm:w-36">
                        <input type="date" name="tanggal_dari" 
                            value="{{ request('tanggal_dari') }}"
                            class="w-full py-1.5 pl-2 bg-transparent border-none text-gray-700 text-sm focus:ring-0 cursor-pointer"
                            placeholder="Dari">
                    </div>

                    <span class="text-gray-400 text-xs font-medium">s/d</span>

                    {{-- Tanggal Sampai --}}
                    <div class="relative flex-1 sm:w-36">
                        <input type="date" name="tanggal_sampai" 
                            value="{{ request('tanggal_sampai') }}"
                            class="w-full py-1.5 pl-1 bg-transparent border-none text-gray-700 text-sm focus:ring-0 cursor-pointer text-right"
                            placeholder="Sampai">
                    </div>
                </div>

                {{-- Tombol Filter --}}
                <button type="submit"
                    class="inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-2.5 rounded-xl shadow-md shadow-indigo-200 hover:bg-indigo-700 transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i data-feather="filter" class="w-4 h-4 mr-2"></i>
                    <span class="font-medium text-sm">Filter</span>
                </button>

            </form>
        </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-b-lg" >
            <div class="p-6 sm:px-8 bg-white border-b border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Masuk</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Pulang</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($data as $row)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-base text-gray-700">{{ \Carbon\Carbon::parse($row->tanggal)->isoFormat('D MMM YYYY') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-gray-900">{{ $row->pegawai->user->name }}</td>
                                   {{-- Absen Masuk --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($row->jenis)
                                            <span class="text-base text-blue-600 font-semibold">
                                                {{ ucfirst($row->jenis) }}
                                            </span>
                                        @else
                                            <div class="text-base text-gray-900">
                                                {{ $row->check_in ? \Carbon\Carbon::parse($row->check_in)->format('H:i:s') : '-' }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Absen Pulang --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($row->jenis)
                                            <span class="text-base text-blue-600 font-semibold">
                                                {{ ucfirst($row->jenis) }}
                                            </span>
                                        @else
                                            @if ($row->check_out)
                                                <div class="text-base text-gray-900">
                                                    {{ \Carbon\Carbon::parse($row->check_out)->format('H:i:s') }}
                                                </div>
                                            @else
                                                <span class="text-base text-gray-400 italic">Belum Absen Pulang</span>
                                            @endif
                                        @endif
                                    </td>
                               {{-- <td class="px-6 py-4 whitespace-nowrap text-base text-gray-700">{{ $row->keterangan ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right text-base">
                                        <a href="{{ route('atasan.kehadiran.show', $row->id) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-medium">
                                            <i data-feather="edit" class="w-4 h-4 mr-1"></i>
                                            Detail / Koreksi
                                        </a>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-base text-gray-500">Belum ada data kehadiran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($data->hasPages())
                        <div class="mt-6 p-4 bg-gray-50 border-t rounded-b-lg">
                            {{ $data->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-app-layout>