<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detail Pengajuan Izin / Sakit
        </h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow-lg p-6 rounded-lg">

            <p class="font-medium text-lg mb-2">Pegawai: {{ $izin->pegawai->user->name }}</p>
            <p class="mb-2">Jenis: <strong class="capitalize">{{ $izin->jenis }}</strong></p>
            <p class="mb-2">Mulai: {{ $izin->tanggal_mulai }}</p>
            <p class="mb-2">Selesai: {{ $izin->tanggal_selesai }}</p>

            @if ($izin->file_surat)
                <p class="mb-2">Lampiran:
                    <a href="{{ asset('storage/' . $izin->file_surat) }}" target="_blank"
                       class="text-blue-600 underline">Lihat File</a>
                </p>
            @endif

            <p>Status:
                <strong class="capitalize">{{ $izin->status }}</strong>
            </p>

            <div class="mt-6 flex gap-4">

                <form action="{{ route('atasan.izin.approve', $izin->id) }}" method="POST">
                    @csrf
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        Setujui
                    </button>
                </form>

                <form action="{{ route('atasan.izin.reject', $izin->id) }}" method="POST">
                    @csrf
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                        Tolak
                    </button>
                </form>

            </div>

        </div>

    </div>

</x-app-layout>
