<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Manajemen Izin & Sakit Pegawai
        </h2>
    </x-slot>

    <div class="py-12  mx-auto sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    
                    <a href="{{ route('atasan.kehadiran.index') }}"
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                        <i data-feather="clock" class="w-5 h-5 mr-2"></i>
                        Rekap Kehadiran
                    </a>

                    <a href="{{ route('atasan.izin.index') }}"
                       class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                        <i data-feather="file-text" class="w-5 h-5 mr-2"></i>
                        Izin / Sakit / Cuti
                    </a>
                </nav>
            </div>
        </div>
            
        @if (session('success'))
            <div class="mb-4 p-4 flex items-center bg-green-100 text-green-700 border border-green-300 rounded-lg text-base">
                <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 flex items-center bg-red-100 text-red-700 border border-red-300 rounded-lg text-base">
                <i data-feather="alert-circle" class="w-5 h-5 mr-3 text-red-500"></i>
                {{ session('error') }}
            </div>
        @endif
        

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            {{-- HEADER CARD --}}
        <div class="px-6 py-5 mb-4 border bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-t-lg shadow-sm">

            {{-- LEFT: ICON + TITLE --}}
            <div class="flex items-center gap-3">
                <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                    <i data-feather="file-text" class="w-6 h-6"></i>
                </span>
                <div>
                <h3 class="text-xl font-bold text-gray-800">Izin / Sakit Pegawai</h3>
                <p class="text-base text-gray-500">Kelola dan verifikasi pengajuan izin atau sakit pegawai.</p>

                </div>
            </div>

        </div>

            <div class="p-6 sm:px-8 bg-white border-b border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">

                            @forelse($data as $row)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-base font-medium text-gray-900">{{ $row->pegawai->user->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-base text-center text-gray-700 capitalize">{{ $row->jenis }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-base text-gray-700">
                                        {{ \Carbon\Carbon::parse($row->tanggal_mulai)->isoFormat('D MMM') }}
                                        - 
                                        {{ \Carbon\Carbon::parse($row->tanggal_selesai)->isoFormat('D MMM YYYY') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($row->status == 'menunggu')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Menunggu
                                        </span>
                                    @elseif($row->status == 'disetujui')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center items-center text-base font-medium">
                                    <div class="justify-center">

                                        <a href="{{ route('atasan.izin.show', $row->id) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                            <i data-feather="eye" class="w-4 h-4 mr-1"></i>
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-base text-gray-500">
                                    Belum ada data izin/sakit.
                                </td>
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