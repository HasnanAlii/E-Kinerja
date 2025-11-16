<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Tambah Progress SKP</h2>
    </x-slot>

    <div class="p-4">
        <form action="{{ route('pegawai.skp-progress.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 shadow rounded">
            @csrf
            
            <div class="mb-3">
                <label>Pilih Target SKP</label>
                <select name="skp_id" class="w-full border p-2 rounded">
                    @foreach ($skp as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_target }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Persentase</label>
                <input type="number" name="persentase" class="w-full border p-2 rounded" min="0" max="100">
            </div>

            <div class="mb-3">
                <label>Bukti File</label>
                <input type="file" name="bukti_file" class="w-full">
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        </form>
    </div>
</x-app-layout>
