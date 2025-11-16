<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Pengajuan Izin / Sakit
        </h2>
    </x-slot>

    <div class="">
       <div class=" mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="{{ route('pegawai.kehadiran.index') }}"
                           class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                            <i data-feather="clock" class="w-5 h-5 mr-2"></i>
                            Rekap Kehadiran
                        </a>
                        <a href="{{ route('pegawai.izin.index') }}"
                         class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                            <i data-feather="file-text" class="w-5 h-5 mr-2"></i>
                            Izin / Sakit
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

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
              <div class="px-6 py-5 border-b bg-gray-50 flex items-center justify-between mb-4 rounded-t-lg">
                <div class="flex items-center gap-3">
                    <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                        <i data-feather="file-text" class="w-6 h-6"></i>
                    </span>

                    <div>
                        <h3 class="text-lg font-bold text-gray-800">
                            Riwayat Izin / Sakit
                        </h3>
                        <p class="text-base text-gray-500">
                            Lihat dan pantau seluruh pengajuan izin atau sakit yang Anda ajukan.
                        </p>
                    </div>
                </div>

                <div class="flex gap-4  justify-end">
                    <a href="{{ route('pegawai.izin.create') }}" 
                    class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-base">
                        <i data-feather="plus" class="w-5 h-5 mr-2 -ml-1"></i>
                        Buat Pengajuan
                    </a>
                </div>
            </div>
                <div class="p-6 sm:px-8 bg-white border-b border-gray-200">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Jenis
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Mulai
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Selesai
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($data as $row)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base text-gray-900 capitalize font-medium">
                                                {{ $row->jenis }}
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base text-gray-700">
                                                {{ \Carbon\Carbon::parse($row->tanggal_mulai)->isoFormat('D MMMM YYYY') }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base text-gray-700">
                                                {{ \Carbon\Carbon::parse($row->tanggal_selesai)->isoFormat('D MMMM YYYY') }}
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($row->status == 'menunggu')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Menunggu
                                                </span>
                                            @elseif($row->status == 'disetujui')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Disetujui
                                                </span>
                                            @elseif($row->status == 'ditolak')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Ditolak
                                                </span>
                                            @else
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 capitalize">
                                                    {{ $row->status }}
                                                </span>
                                            @endif
                                        </td>
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-base text-gray-500">
                                            Belum ada data pengajuan izin.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

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