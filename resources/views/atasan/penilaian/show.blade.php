<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            SKP Pegawai: {{ $pegawai->nama_lengkap }}
        </h2>
    </x-slot>

    <div class="py-6 mx-auto max-w-7xl">

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4">Daftar SKP</h3>

            <table class="w-full text-left mb-4">
                <thead>
                    <tr class="border-b bg-gray-100">
                        <th class="py-2 px-3">Target</th>
                        <th class="py-2 px-3">Periode</th>
                        <th class="py-2 px-3">Status</th>
                        <th class="py-2 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($skp as $s)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-3">{{ $s->nama_target }}</td>
                            <td class="py-2 px-3">{{ $s->periode }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 rounded text-white
                                    {{ $s->status == 'Diajukan' ? 'bg-yellow-500' :
                                       ($s->status == 'Dinilai' ? 'bg-blue-600' :
                                       ($s->status == 'Selesai' ? 'bg-green-600' : 'bg-gray-500')) }}">
                                    {{ $s->status }}
                                </span>
                            </td>
                            <td class="py-2 px-3">
                                <a href="{{ route('atasan.skp.show', $s->id) }}"
                                    class="text-blue-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">Belum ada SKP</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

    </div>
</x-app-layout>
