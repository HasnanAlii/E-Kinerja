<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Daftar Target SKP</h2>
    </x-slot>

    <div class="p-4">
        <div class="bg-white p-4 rounded shadow">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="py-2">Target</th>
                        <th>Indikator</th>
                        <th>Periode</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($data as $row)
                        <tr class="border-b">
                            <td class="py-2">{{ $row->nama_target }}</td>
                            <td>{{ $row->indikator }}</td>
                            <td>{{ $row->periode }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
