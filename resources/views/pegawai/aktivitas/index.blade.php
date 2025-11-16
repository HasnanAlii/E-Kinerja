<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Daftar Aktivitas Harian
        </h2>
    </x-slot>

    <div class="py-12">
        <div class=" mx-auto sm:px-6 lg:px-8">
   

            @if (session('success'))
                <div class="mb-4 p-4 flex items-center bg-green-100 text-green-700 border border-green-300 rounded-lg">
                    <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center justify-between mb-4 rounded-t-lg">
                <div class="flex items-center gap-3">
                    <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                        <i data-feather="list" class="w-6 h-6"></i>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">
                            Riwayat Aktivitas Harian
                        </h3>
                        <p class="text-base text-gray-500">
                            Lihat, kelola, dan pantau aktivitas harian yang telah Anda laporkan.
                        </p>
                    </div>
                </div>
                    <div class="flex gap-4  justify-end">
                        <a href="{{ route('pegawai.aktivitas.create') }}" 
                        class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <i data-feather="plus" class="w-5 h-5 mr-2 -ml-1"></i>
                            Tambah Aktivitas
                        </a>
                    </div>
            </div>

                <div class="p-6 sm:px-8 bg-white border-b border-gray-200">
                    
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Uraian
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($data as $row)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base text-gray-900">
                                                {{-- Format tanggal agar lebih rapi --}}
                                                {{ \Carbon\Carbon::parse($row->tanggal)->isoFormat('D MMMM YYYY') }}
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <div class="text-base text-gray-700 whitespace-normal">
                                                {{ $row->uraian_tugas }}
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
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium">
                                            <div class="flex items-center space-x-4">
                                                @if ($row->status == 'menunggu')
                                                    <a href="{{ route('pegawai.aktivitas.edit', $row->id) }}" 
                                                       class="flex items-center text-indigo-600 hover:text-indigo-900" 
                                                       title="Edit">
                                                       <i data-feather="edit-2" class="w-4 h-4 mr-1"></i> Edit
                                                    </a>
                                                    
                                                    <form method="POST" action="{{ route('pegawai.aktivitas.destroy', $row->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aktivitas ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="flex items-center text-red-600 hover:text-red-900" 
                                                                title="Hapus">
                                                            <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="flex items-center text-gray-400 cursor-not-allowed" title="Terkunci">
                                                        <i data-feather="lock" class="w-4 h-4 mr-1"></i> Terkunci
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-base text-gray-500">
                                            Belum ada data aktivitas harian.
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