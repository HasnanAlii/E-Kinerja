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

            {{-- TABLE --}}
            <div class="p-6 sm:px-8 bg-white border-gray-200">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Pegawai
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Bidang
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($bawahan as $item)
                                <tr class="hover:bg-gray-50 transition-colors">

                                    {{-- NAMA PEGAWAI --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-base font-medium text-gray-900">
                                            {{ $item->user->name }}
                                        </div>
                                    </td>

                                    {{-- BIDANG --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-base text-gray-700">
                                            {{ $item->bidang->nama_bidang }}
                                        </div>
                                    </td>

                                    {{-- AKSI --}}
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
