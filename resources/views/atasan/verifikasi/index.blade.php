<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold text-gray-800">
            Verifikasi Aktivitas Pegawai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">

            {{-- CARD WRAPPER --}}
            <div class="bg-white shadow-xl sm:rounded-xl border border-gray-200 overflow-hidden">

                {{-- HEADER --}}
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                          <i data-feather="clipboard" class="w-6 h-6"></i>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Daftar Aktivitas Menunggu</h3>
                            <p class="text-base text-gray-500">
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
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($data as $row)
                <tr class="hover:bg-gray-50 transition-colors">

                    {{-- FOTO + NAMA --}}
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

                    {{-- TANGGAL --}}
                    <td class="px-6 py-4 whitespace-nowrap text-base text-gray-700">
                        {{ \Carbon\Carbon::parse($row->tanggal)->isoFormat('D MMM YYYY') }}
                    </td>

                    {{-- URAIAN --}}
                    <td class="px-6 py-4 whitespace-nowrap text-base text-gray-700">
                        {{ Str::limit($row->uraian_tugas, 60) }}
                    </td>

                    {{-- STATUS --}}
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if ($row->status === 'menunggu')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                Menunggu
                            </span>
                        @elseif ($row->status === 'disetujui')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                Disetujui
                            </span>
                        @elseif ($row->status === 'ditolak')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                Ditolak
                            </span>
                        @elseif ($row->status === 'revisi')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                Revisi
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">-</span>
                        @endif
                    </td>

                    {{-- AKSI --}}
                    <td class="px-6 py-4 whitespace-nowrap text-center text-base">
                        <a href="{{ route('atasan.verifikasi.show', $row->id) }}" 
                            class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-medium">
                            <i data-feather="eye" class="w-4 h-4 mr-1"></i>
                            Detail
                        </a>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center text-base text-gray-500">
                        Tidak ada aktivitas menunggu verifikasi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- PAGINATION --}}
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
