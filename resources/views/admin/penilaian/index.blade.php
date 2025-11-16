<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Laporan Penilaian Pegawai
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
                <div class="p-6 sm:px-8 bg-white">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Nilai Total</th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th scope="col" class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($data as $row)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base font-medium text-gray-900">{{ $row->pegawai->user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base text-gray-700">{{ $row->periode->nama_periode }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base font-semibold text-gray-900">{{ number_format($row->nilai_total, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($row->kategori == 'Sangat Baik')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Sangat Baik
                                                </span>
                                            @elseif($row->kategori == 'Baik')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Baik
                                                </span>
                                            @elseif($row->kategori == 'Cukup')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Cukup
                                                </span>
                                            @elseif($row->kategori == 'Kurang')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Kurang
                                                </span>
                                            @else
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 capitalize">
                                                    {{ $row->kategori }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-right">
                                            <a href="{{ route('admin.penilaian.show', $row->id) }}" 
                                               class="inline-flex items-center text-green-600 hover:text-green-900">
                                                <i data-feather="eye" class="w-4 h-4 mr-1"></i>
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-6 text-center text-base text-gray-500">
                                            Belum ada data penilaian.
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