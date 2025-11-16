<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Verifikasi Aktivitas Pegawai
        </h2>
    </x-slot>

    <div class="py-4">
        <div class=" mx-auto sm:px-6 lg:px-8">
            {{-- CARD WRAPPER --}}
            <div class="bg-white shadow-xl sm:rounded-xl border border-gray-200 overflow-hidden">

                {{-- HEADER --}}
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                            <i data-feather="activity" class="w-5 h-5"></i>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Daftar Aktivitas Menunggu</h3>
                            <p class="text-sm text-gray-500">
                                Aktivitas yang perlu Anda verifikasi sebagai atasan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="px-6 py-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Pegawai</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Aktivitas</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Status</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($data as $row)
                                <tr>
                                    {{-- FOTO + NAMA --}}
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <img src="{{ asset('storage/' . ($row->pegawai->foto ?? 'default.png')) }}"
                                             class="h-10 w-10 rounded-full object-cover border">
                                        <div>
                                            <div class="font-semibold text-gray-800">
                                                {{ $row->pegawai->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $row->pegawai->jabatan ?? '-' }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- TANGGAL --}}
                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        {{ \Carbon\Carbon::parse($row->tanggal)->isoFormat('D MMMM YYYY') }}
                                    </td>

                                    {{-- URAIAN --}}
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ Str::limit($row->uraian_tugas, 50) }}
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                            Menunggu
                                        </span>
                                    </td>

                                    {{-- AKSI --}}
                                     <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-center" >
                                            <a href="{{ route('atasan.verifikasi.show', $row->id) }}" 
                                                class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                                <i data-feather="eye" class="w-4 h-4 mr-1"></i>
                                                Lihat Detail
                                            </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-6 text-center text-gray-500" colspan="5">
                                        Tidak ada aktivitas menunggu verifikasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $data->links() }}
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>

                                      
                                