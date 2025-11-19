<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Laporan Penilaian Pegawai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class=" mx-auto sm:px-6 lg:px-8">
                    

        <div class="bg-white shadow-xl sm:rounded-2xl overflow-hidden border border-gray-100">

            <div class="px-6 py-6 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl shadow-sm ring-1 ring-indigo-100">
                        <i data-feather="bar-chart-2" class="w-6 h-6"></i>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Laporan Penilaian Pegawai</h3>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Pilih periode untuk melihat rekap nilai kinerja.
                        </p>
                    </div>
                </div>

            {{-- 2. FILTER SECTION --}}
                <form method="GET" action="{{ route('atasan.laporan.index') }}" class="flex flex-col sm:flex-row gap-3">

                    {{-- Filter Periode Dropdown --}}
                    <div class="relative w-full sm:w-64">
                        <label for="periode_id" class="sr-only">Filter Periode</label>
                        
                        {{-- Icon Kiri --}}
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i data-feather="calendar" class="w-4 h-4"></i>
                        </div>

                        <select name="periode_id" id="periode_id"
                            class="w-full pl-10 pr-8 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 cursor-pointer appearance-none hover:bg-gray-50">
                            <option value="">Semua Periode</option>
                            @foreach ($periode as $p)
                                <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_periode }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Icon Kanan (Chevron) --}}
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                            <i data-feather="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>

                    {{-- Tombol Filter --}}
                    <button type="submit"
                        class="inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-2.5 rounded-xl shadow-md shadow-indigo-200 hover:bg-indigo-700 transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto w-full">
                        <i data-feather="filter" class="w-4 h-4 mr-2"></i>
                        <span class="font-medium text-sm">Filter</span>
                    </button>

                </form>
            </div>


            <div class="bg-white overflow-hidden shadow-xl sm:rounded-b-xl">
                 <div class=" bg-white border-gray-200">        
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
                                            @if ($row->pegawai->user->profile_photo)
                                                <img 
                                                    src="{{ asset('storage/' . $row->pegawai->user->profile_photo) }}"
                                                    class="h-10 w-10 rounded-full object-cover border shadow-sm"
                                                    alt="Foto Profil"
                                                >
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center border shadow-sm">
                                                    <div class="w-full h-full rounded-full bg-indigo-50 flex items-center justify-center text-indigo-300">
                                                        <i data-feather="user" class="w-5 h-5"></i>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="flex flex-col">
                                                <span class="text-base font-semibold text-gray-900">
                                                    {{ $row->pegawai->user->name }}
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    {{ $row->pegawai->jabatan ?? '-' }}
                                                </span>
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