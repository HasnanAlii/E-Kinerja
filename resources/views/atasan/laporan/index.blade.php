<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Laporan Penilaian Pegawai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class=" mx-auto sm:px-6 lg:px-8">
            

            <div class="px-6 py-5 border-b bg-gray-50 flex flex-col rounded-t-xl sm:flex-row sm:items-center sm:justify-between gap-4">
                {{-- LEFT: HEADER --}}
                <div class="flex items-center gap-3 ">
                    <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                        <i data-feather="bar-chart-2" class="w-6 h-6"></i>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Laporan Penilaian Pegawai</h3>
                        <p class="text-base text-gray-500">
                            Pilih periode untuk melihat rekap penilaian.
                        </p>
                    </div>
                </div>

                {{-- RIGHT: FILTER --}}
                <form method="GET" action="{{ route('atasan.laporan.index') }}" 
                    class="flex items-end gap-4">

                    <div>
                        <label for="periode_id" class="block text-sm font-medium text-gray-700">Filter Periode</label>
                        <select name="periode_id" id="periode_id"
                                class="mt-1 block w-48 sm:w-60 rounded-md border-gray-300 shadow-sm 
                                    focus:border-indigo-500 focus:ring-indigo-500 text-base">
                            <option value="">Semua Periode</option>
                            @foreach ($periode as $p)
                                <option value="{{ $p->id }}" 
                                    {{ request('periode_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_periode }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 
                                rounded-lg shadow-sm hover:bg-indigo-700 transition 
                                duration-150 ease-in-out focus:outline-none focus:ring-2 
                                focus:ring-indigo-500 focus:ring-offset-2 text-base">
                        <i data-feather="filter" class="w-5 h-5 mr-2 -ml-1"></i>
                        Filter
                    </button>

                </form>
            </div>


            <div class="bg-white overflow-hidden shadow-xl sm:rounded-b-xl">
                <div class="p-6 sm:px-8 bg-white border-b border-gray-200">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Pegawai
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Periode
                                    </th>
                                    {{-- <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Nilai Akhir
                                    </th> --}}
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($data as $row)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 flex items-center gap-3 whitespace-nowrap">
                                        <img src="{{ asset('storage/' . ($row->pegawai->user->profile_photo ?? 'default.png')) }}"
                                            class="h-10 w-10 rounded-full object-cover border shadow-sm">

                                        <div>
                                            <div class="text-base font-medium text-gray-900">
                                                {{ $row->pegawai->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $row->pegawai->jabatan ?? '-' }}
                                            </div>
                                        </div>
                                    </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base text-gray-700">
                                                {{ $row->periode->nama_periode }}
                                            </div>
                                        </td>

                                        {{-- <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base font-semibold text-gray-900">
                                                {{ number_format($row->nilai_total, 2) }}
                                            </div>
                                        </td> --}}

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
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium">
                                            <a href="{{ route('atasan.laporan.show', $row->id) }}" 
                                               class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                               <i data-feather="eye" class="w-4 h-4 mr-1"></i>
                                               Detail
                                            </a>
                                        </td>
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-base text-gray-500">
                                            Tidak ada data laporan untuk ditampilkan.
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