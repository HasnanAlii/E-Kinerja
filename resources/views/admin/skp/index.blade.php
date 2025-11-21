<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <i data-feather="file-text" class="w-5 h-5 text-indigo-600"></i>
            Manajemen SKP Pegawai
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl shadow border p-6">

                <table class="w-full table-auto text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left">Pegawai</th>
                            <th class="px-4 py-2 text-left">Bidang</th>
                            <th class="px-4 py-2 text-left">Periode</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($skp as $item)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $item->pegawai->user->name }}</td>
                                <td class="px-4 py-2">{{ $item->bidang->nama_bidang ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $item->periode }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded-lg text-xs font-semibold
                                        @if($item->status=='Draft') bg-gray-100 text-gray-700
                                        @elseif($item->status=='Diajukan') bg-blue-100 text-blue-700
                                        @elseif($item->status=='Dinilai') bg-green-100 text-green-700
                                        @elseif($item->status=='Final') bg-indigo-100 text-indigo-700
                                        @endif
                                    ">
                                        {{ $item->status }}
                                    </span>
                                </td>

                                <td class="px-4 py-2 text-center flex justify-center gap-3">
                                    <a href="{{ route('admin.skp.show', $item->id) }}"
                                       class="text-indigo-600 font-medium hover:text-indigo-800">
                                        Detail
                                    </a>

                                    @if($item->status === 'Dinilai')
                                        <a href="{{ route('admin.skp.final', $item->id) }}"
                                           class="text-green-600 font-medium hover:text-green-800">
                                            Finalisasi
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">
                                    Tidak ada data SKP.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $skp->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
