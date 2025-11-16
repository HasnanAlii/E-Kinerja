<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Progress Capaian SKP</h2>
    </x-slot>

    <div class="p-4">
        <a href="{{ route('pegawai.skp-progress.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
            + Tambah Progress
        </a>

        <div class="bg-white p-4 rounded shadow">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="py-2">Target SKP</th>
                        <th>Progress</th>
                        <th>Tanggal Update</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($data as $row)
                        <tr class="border-b">
                            <td class="py-2">{{ $row->skp->nama_target }}</td>
                            <td>{{ $row->persentase }}%</td>
                            <td>{{ $row->tanggal_update }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $data->links() }}
        </div>
    </div>
</x-app-layout>
