<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Laporan Kehadiran</h2>
    </x-slot>

    <div class="py-6 mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-2xl overflow-hidden border border-gray-100">
            <div class="px-6 py-5 border-b bg-gray-50 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Laporan Kehadiran Karyawan</h3>
                    <p class="text-sm text-gray-500">Pilih rentang tanggal untuk melihat rekap kehadiran.</p>
                </div>
                <form method="GET" action="{{ route('atasan.laporan.index') }}" class="flex flex-wrap items-center gap-2">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama karyawan..." class="rounded-lg border-gray-300 text-sm">
                    <input type="date" name="tanggal_dari" value="{{ $dari }}" class="rounded-lg border-gray-300 text-sm">
                    <span class="text-gray-400 text-sm">s/d</span>
                    <input type="date" name="tanggal_sampai" value="{{ $sampai }}" class="rounded-lg border-gray-300 text-sm">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Filter</button>
                    <a href="{{ route('atasan.laporan.export.pdf', ['cari' => request('cari'), 'tanggal_dari' => $dari, 'tanggal_sampai' => $sampai]) }}" target="_blank"
                       class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">PDF</a>
                    <a href="{{ route('atasan.laporan.export.excel', ['cari' => request('cari'), 'tanggal_dari' => $dari, 'tanggal_sampai' => $sampai]) }}"
                       class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">Excel</a>
                </form>
            </div>

            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Nama Karyawan</th>
                        <th class="px-4 py-3 text-center">Hadir</th>
                        <th class="px-4 py-3 text-center">Izin</th>
                        <th class="px-4 py-3 text-center">Sakit</th>
                        <th class="px-4 py-3 text-center">Cuti</th>
                        <th class="px-4 py-3 text-center">Alpha</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($rekap as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $r->nama }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->hadir }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->izin }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->sakit }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->cuti }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->alpha }}</td>
                            <td class="px-4 py-3 text-center">
                                <a target="_blank" class="text-red-600 hover:underline"
                                   href="{{ route('laporan.kehadiran.perpegawai', ['pegawai_id' => $r->id, 'tanggal_dari' => $dari, 'tanggal_sampai' => $sampai]) }}">PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada data karyawan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
