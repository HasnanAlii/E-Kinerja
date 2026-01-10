<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Pegawai Bawahan</h2>
    </x-slot>

    <div class="py-6 mx-auto max-w-7xl">

        <div class="bg-white shadow rounded-lg p-6">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b bg-gray-100">
                        <th class="py-2 px-3">Nama</th>
                        <th class="py-2 px-3">Bidang</th>
                        <th class="py-2 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pegawai as $p)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-3">{{ $p->nama_lengkap }}</td>
                            <td class="py-2 px-3">{{ $p->bidang->nama }}</td>
                            <td class="py-2 px-3">


                        <a href="{{ route('laporan.kehadiran.perpegawai', $data->id) }}"
                            target="_blank"
                            class="flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                                <i data-feather="file-text" class="w-4 h-4 mr-2"></i>
                                Laporan Kehadiran PDF
                            </a>
                                <a href="{{ route('atasan.pegawai.show', $p->id) }}"
                                    class="text-blue-600 hover:underline">Lihat SKP</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $pegawai->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
