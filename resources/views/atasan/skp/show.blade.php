<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Detail SKP: {{ $skp->nama_target }}
        </h2>
    </x-slot>

    <div class="py-6 mx-auto max-w-5xl">

        <div class="bg-white shadow rounded-lg p-6">

            {{-- Informasi SKP --}}
            <h3 class="text-lg font-bold">Informasi SKP</h3>
            <div class="mt-3 grid grid-cols-2 gap-4">

                <div>
                    <p class="text-gray-500">Nama Pegawai</p>
                    <p class="font-semibold">{{ $skp->pegawai->nama_lengkap }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Periode</p>
                    <p class="font-semibold">{{ $skp->periode }}</p>
                </div>

                <div class="col-span-2">
                    <p class="text-gray-500">Indikator</p>
                    <p>{{ $skp->indikator }}</p>
                </div>

            </div>

            {{-- Status --}}
            <div class="mt-5">
                <span class="px-3 py-1 rounded text-white
                    {{ $skp->status == 'Diajukan' ? 'bg-yellow-500' :
                       ($skp->status == 'Dinilai' ? 'bg-blue-600' :
                       ($skp->status == 'Selesai' ? 'bg-green-600' : 'bg-gray-500')) }}">
                    Status: {{ $skp->status }}
                </span>
            </div>

            {{-- Aksi Atasan --}}
            <div class="mt-6 flex gap-2">

                {{-- Setujui --}}
                <form action="{{ route('atasan.skp.updateStatus', $skp->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Diajukan">
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Setujui SKP
                    </button>
                </form>

                {{-- Tolak --}}
                <form action="{{ route('atasan.skp.updateStatus', $skp->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Draft">
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        Tolak
                    </button>
                </form>

                {{-- Beri Nilai --}}
                <button onclick="document.getElementById('nilaiModal').showModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Beri Nilai
                </button>

            </div>

            {{-- Daftar Progres --}}
            <h3 class="text-lg font-bold mt-8 mb-3">Progres SKP</h3>

            @forelse ($progres as $p)
                <div class="border-l-4 border-blue-600 pl-3 mb-4">
                    <p class="font-semibold">{{ $p->tanggal_update }}</p>
                    <p>{{ $p->keterangan }}</p>

                    @if ($p->bukti_file)
                        <a href="{{ asset('storage/' . $p->bukti_file) }}"
                            class="text-blue-600 underline" target="_blank">Lihat Bukti</a>
                    @endif

                    <p class="text-sm text-gray-500">Progress: {{ $p->persentase }}%</p>
                </div>
            @empty
                <p class="text-gray-500">Belum ada progres.</p>
            @endforelse

        </div>

    </div>

    {{-- MODAL NILAI --}}
    <dialog id="nilaiModal" class="rounded-xl p-5 w-96 shadow-xl">

        <form method="POST" action="{{ route('atasan.skp.nilai', $skp->id) }}">
            @csrf

            <h3 class="text-xl font-bold mb-3">Nilai SKP</h3>

            <label class="block mb-2">Nilai (0 - 100)</label>
            <input type="number" name="nilai_capaian" class="w-full border rounded px-3 py-2"
                   min="0" max="100" required>

            <label class="block mt-4 mb-2">Catatan</label>
            <textarea name="catatan" rows="3" class="w-full border rounded px-3 py-2"></textarea>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="document.getElementById('nilaiModal').close()"
                        class="px-4 py-2 bg-gray-300 rounded">Batal</button>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                    Simpan
                </button>
            </div>
        </form>

    </dialog>
</x-app-layout>
