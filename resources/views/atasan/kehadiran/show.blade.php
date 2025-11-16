<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detail Kehadiran
        </h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow sm:rounded-lg p-6">
            <p class="font-medium">Pegawai: {{ $row->pegawai->user->name }}</p>
            <p>Tanggal: {{ \Carbon\Carbon::parse($row->tanggal)->isoFormat('D MMMM Y') }}</p>

            <div class="mt-4">
                <form action="{{ route('atasan.kehadiran.update', $row->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Check In (Y-m-d H:i:s)</label>
                            <input type="text" name="check_in" value="{{ old('check_in', $row->check_in ? \Carbon\Carbon::parse($row->check_in)->format('Y-m-d H:i:s') : '') }}" class="mt-1 block w-full rounded-md border-gray-300" placeholder="2025-11-16 08:00:00">
                            <p class="text-xs text-gray-400 mt-1">Biarkan kosong agar tidak diubah.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Check Out (Y-m-d H:i:s)</label>
                            <input type="text" name="check_out" value="{{ old('check_out', $row->check_out ? \Carbon\Carbon::parse($row->check_out)->format('Y-m-d H:i:s') : '') }}" class="mt-1 block w-full rounded-md border-gray-300" placeholder="2025-11-16 17:00:00">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Keterangan (opsional)</label>
                            <textarea name="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300">{{ old('keterangan', $row->keterangan ?? '') }}</textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">Simpan Perubahan</button>
                            <a href="{{ route('atasan.kehadiran.index') }}" class="inline-flex items-center border border-gray-300 px-4 py-2 rounded-md">Kembali</a>
                        </div>
                    </div>
                </form>
            </div>

            @if($row->file ?? false)
                <div class="mt-4">
                    <a href="{{ asset('storage/' . $row->file) }}" target="_blank" class="text-blue-600 underline">Lihat Lampiran</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
