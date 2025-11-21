<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold leading-tight text-gray-800">
            Penilaian Kinerja Pegawai
        </h2>
    </x-slot>

    <div class="py-12 mx-auto sm:px-6 lg:px-8">

        {{-- INFO PERIODE --}}
        <div class="mb-4 p-4 flex items-center bg-blue-100 text-blue-800 rounded-lg border border-blue-200">
            <i data-feather="info" class="w-5 h-5 mr-3 text-blue-600"></i>
            <div class="text-base">
                Periode aktif: 
                <strong>{{ $periodeAktif->nama_periode ?? 'Belum Ada Periode Penilaian Aktif' }}</strong>
            </div>
        </div>

        {{-- CARD WRAPPER --}}
        <div class="bg-white shadow-xl sm:rounded-xl border border-gray-200 overflow-hidden">

            {{-- HEADER CARD --}}
            <div class="px-6 py-5 border-b bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                        <i data-feather="clipboard" class="w-6 h-6"></i>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Daftar Pegawai untuk Dinilai</h3>
                        <p class="text-base text-gray-500">
                            Pilih pegawai yang akan Anda lakukan penilaian kinerjanya.
                        </p>
                    </div>
                </div>
            </div>

            
            <div class=" bg-white border-gray-200">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Pegawai
                                </th>
                                 <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Jabatan
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Bidang
                                </th>
                                 <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($bawahan as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 flex items-center gap-3 whitespace-nowrap">
                                        @if ($item->user->profile_photo)
                                            <img 
                                                src="{{ asset('storage/' . $item->user->profile_photo) }}"
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
                                                {{ $item->user->name }}
                                            </span>
                                            {{-- <span class="text-sm text-gray-500">
                                                {{ $item->jabatan }}
                                            </span> --}}
                                        </div>
                                    </td>
                            

                                   
                                    <td class="px-6 py-4 whitespace-nowrap">
                                      {{-- BIDANG --}}
                                       <div class="text-sm text-gray-500">
                                            {{ $item->user->pegawaiDetail->jabatan ?? '-' }}
                                        </div>

                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-base text-gray-700">
                                            {{ $item->bidang->nama_bidang }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-base">

                                            @if ($item->sudah_dinilai == 0)
                                                <span class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-full">
                                                    Belum Dinilai
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-full">
                                                    Sudah Dinilai
                                                </span>
                                            @endif

                                        </div>
                                    </td>


                                    {{-- AKSI --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-base font-medium">
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

                {{-- PAGINATION --}}
                @if ($bawahan->hasPages())
                    <div class="mt-6 p-4 bg-gray-50 border-t rounded-b-lg">
                        {{ $bawahan->links() }}
                    </div>
                @endif

            </div>
        </div>

    </div>
</x-app-layout>
