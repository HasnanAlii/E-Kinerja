<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Manajemen Periode Penilaian
        </h2>
    </x-slot>

    <div class="py-12">
        <div class=" mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 flex items-center bg-green-100 text-green-700 border border-green-300 rounded-lg text-base">
                    <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                {{-- HEADER --}}
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                            <i data-feather="calendar" class="w-6 h-6"></i>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">
                                Manajemen Periode Penilaian
                            </h3>
                            <p class="text-base text-gray-500">
                                Kelola periode penilaian kinerja pegawai dengan mudah dan terstruktur.
                            </p>
                        </div>
                    </div>
                      <div class="flex justify-end">
                        <a href="{{ route('admin.periode.create') }}"
                        class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-blue-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-base">
                            <i data-feather="plus" class="w-5 h-5 mr-2 -ml-1"></i>
                            Tambah Periode
                        </a>
                      </div>
            </div>

                <div class="p-6 sm:px-8 bg-white">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Nama Periode</th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($data as $row)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base font-medium text-gray-900">{{ $row->nama_periode }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base text-gray-700">{{ \Carbon\Carbon::parse($row->tgl_mulai)->isoFormat('D MMM YYYY') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base text-gray-700">{{ \Carbon\Carbon::parse($row->tgl_selesai)->isoFormat('D MMM YYYY') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($row->status_aktif)
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Non-Aktif
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium">
                                            <div class="flex items-center space-x-4">
                                                <a href="{{ route('admin.periode.edit', $row->id) }}" class="inline-flex items-center text-yellow-600 hover:text-yellow-900">
                                                    <i data-feather="edit" class="w-4 h-4 mr-1"></i>
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.periode.destroy', $row->id) }}" 
                                                      method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?');">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-900">
                                                        <i data-feather="trash-2" class="w-4 h-4 mr-1"></i>
                                                        Hapus
                                                    </button>
                                                </form>
                                                
                                                @if(!$row->status_aktif)
                                                    <form action="{{ route('admin.periode.aktifkan', $row->id) }}" method="POST">
                                                        @csrf
                                                        <button class="inline-flex items-center text-green-600 hover:text-green-900">
                                                            <i data-feather="check-circle" class="w-4 h-4 mr-1"></i>
                                                            Aktifkan
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="inline-flex items-center text-gray-400 cursor-not-allowed">
                                                        <i data-feather="check-circle" class="w-4 h-4 mr-1"></i>
                                                        Aktif
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-6 text-center text-base text-gray-500">
                                            Belum ada data periode.
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