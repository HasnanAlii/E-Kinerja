<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Manajemen Kehadiran
        </h2>
    </x-slot>

    <div class="py-12 mx-auto sm:px-6 lg:px-8">
     
     <div class="mb-6">
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
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 flex items-center bg-green-100 text-green-700 rounded-lg border border-green-300 text-base">
                    <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
           {{-- HEADER CARD --}}
            <div class="px-6 py-5 border bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-t-lg shadow-sm">

            {{-- LEFT: ICON + TITLE --}}
            <div class="flex items-center gap-3">
                <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                    <i data-feather="clock" class="w-6 h-6"></i>
                </span>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Rekap Kehadiran Pegawai</h3>
                    <p class="text-base text-gray-500">
                        Lihat dan kelola data kehadiran berdasarkan pegawai & tanggal.
                    </p>
                </div>
            </div>


            <form method="GET" class="flex flex-wrap items-end gap-4">

                {{-- Pegawai --}}
                <div class="flex flex-col w-48">
                    <label for="pegawai_id" class="text-sm font-medium text-gray-700">Pegawai</label>
                    <select name="pegawai_id" id="pegawai_id"
                        class="h-10 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">-- Semua --</option>
                        @foreach($pegawais as $p)
                            <option value="{{ $p->id }}" @selected(request('pegawai_id') == $p->id)>
                                {{ $p->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dari --}}
                <div class="flex flex-col w-40">
                    <label for="tanggal_dari" class="text-sm font-medium text-gray-700">Dari</label>
                    <input type="date" name="tanggal_dari" id="tanggal_dari"
                        value="{{ request('tanggal_dari') }}"
                        class="h-10 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                {{-- Sampai --}}
                <div class="flex flex-col w-40">
                    <label for="tanggal_sampai" class="text-sm font-medium text-gray-700">Sampai</label>
                    <input type="date" name="tanggal_sampai" id="tanggal_sampai"
                        value="{{ request('tanggal_sampai') }}"
                        class="h-10 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                {{-- Tombol Filter --}}
                <div>
                    <button type="submit"
                        class="h-10  mt-6 inline-flex items-center px-4 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 text-sm">
                        <i data-feather="filter" class="w-4 h-4 mr-2"></i>
                        Filter
                    </button>
                </div>
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