<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Profil Pegawai</h2>
    </x-slot>

    <div class="p-6">
        <div class="bg-white p-6 rounded shadow max-w-xl mx-auto">

            @if (session('success'))
                <div class="p-3 bg-green-200 rounded mb-3">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('pegawai.profil.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label>NIP</label>
                    <input type="text" name="nip" value="{{ $pegawai->nip }}" class="w-full border p-2 rounded">
                </div>

                <div class="mb-3">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" value="{{ $pegawai->jabatan }}" class="w-full border p-2 rounded">
                </div>

                <div class="mb-3">
                    <label>Foto</label>
                    <input type="file" name="foto" class="w-full">
                </div>

                @if ($pegawai->foto)
                    <img src="{{ asset('storage/'.$pegawai->foto) }}" class="w-24 h-24 rounded-full mb-3">
                @endif

                <button class="bg-blue-600 text-white px-4 py-2 rounded">Update Profil</button>
            </form>
        </div>
    </div>
</x-app-layout>
