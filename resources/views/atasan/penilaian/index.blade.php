<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Penilaian Kinerja Pegawai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class=" mx-auto sm:px-6 lg:px-8">

            <div class="mb-4 p-4 flex items-center bg-blue-100 text-blue-800 rounded-lg border border-blue-200">
                <i data-feather="info" class="w-5 h-5 mr-3 text-blue-600"></i>
                <div class="text-base">
                    Periode aktif: 
                    <strong>{{ $periodeAktif->nama_periode ?? 'Belum Ada Periode Penilaian yang Aktif' }}</strong>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-8 bg-white border-b border-gray-200">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Pegawai
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Bidang
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($bawahan as $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base font-medium text-gray-900">
                                                {{ $item->user->name }}
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base text-gray-700">
                                                {{ $item->bidang->nama_bidang }}
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium">
                                            <a href="{{ route('atasan.penilaian.create', $item->id) }}" 
                                               class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                               <i data-feather="edit-3" class="w-4 h-4 mr-1"></i>
                                               Isi Penilaian
                                            </a>
                                        </td>
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-base text-gray-500">
                                            Anda tidak memiliki data bawahan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>