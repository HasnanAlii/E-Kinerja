<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <i data-feather="folder" class="w-5 h-5 text-indigo-600"></i>
            Detail SKP Pegawai
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <!-- Header Pegawai -->
            <div class="bg-white border shadow rounded-xl p-6 mb-6">
                <h3 class="text-2xl font-semibold text-gray-900">{{ $data->pegawai->user->name }}</h3>
                <p class="text-gray-500">Bidang: {{ $data->bidang->nama_bidang ?? '-' }}</p>
                <p class="text-gray-500">Periode: {{ $data->periode }}</p>

                <div class="mt-3">
                    <span class="px-3 py-1 rounded-lg text-sm font-semibold
                        @if($data->status=='Draft') bg-gray-100 text-gray-700
                        @elseif($data->status=='Diajukan') bg-blue-100 text-blue-700
                        @elseif($data->status=='Dinilai') bg-green-100 text-green-700
                        @elseif($data->status=='Final') bg-indigo-100 text-indigo-700
                        @endif
                    ">
                        Status: {{ $data->status }}
                    </span>
                </div>
            </div>

            <!-- Capaian Organisasi & Pola -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white border shadow rounded-xl p-6">
                    <h4 class="font-semibold text-gray-700 mb-2">Capaian Kinerja Organisasi</h4>
                    <p class="text-sm text-gray-800 whitespace-pre-wrap">
                        {{ $data->capaian_kinerja_organisasi ?? '-' }}
                    </p>
                </div>

                <div class="bg-white border shadow rounded-xl p-6">
                    <h4 class="font-semibold text-gray-700 mb-2">Pola Distribusi</h4>
                    <p class="text-sm text-gray-800 whitespace-pre-wrap">
                        {{ $data->pola_distribusi ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Hasil Kerja -->
            <div class="bg-white border shadow rounded-xl p-6 mb-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-4">Hasil Kerja</h4>

                @forelse($data->hasilKerja as $hk)
                    <div class="p-4 border rounded-lg bg-gray-50 mb-3">
                        <p class="font-bold">{{ $hk->jenis }}</p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                            <div>
                                <p class="text-xs text-gray-500">RHK</p>
                                <p>{{ $hk->rhk }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Target</p>
                                <p>{{ $hk->target }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Realisasi</p>
                                <p>{{ $hk->realisasi }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Belum ada hasil kerja.</p>
                @endforelse
            </div>

            <!-- Perilaku ASN -->
            <div class="bg-white border shadow rounded-xl p-6 mb-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-4">Perilaku ASN</h4>

                @forelse($data->perilaku as $p)
                    <div class="p-4 border rounded-lg bg-gray-50 mb-3">
                        <p class="font-bold">{{ $p->aspek }}</p>
                        <p class="text-xs text-gray-500 mt-2">Perilaku</p>
                        <p>{{ $p->perilaku }}</p>
                        <p class="text-xs text-gray-500 mt-2">Ekspektasi</p>
                        <p>{{ $p->ekspektasi }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">Belum ada perilaku.</p>
                @endforelse
            </div>

            <!-- Rating -->
            <div class="bg-white border shadow rounded-xl p-6 mb-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-4">Nilai Akhir</h4>

                <p><strong>Rating:</strong> {{ $data->rating ?? '-' }}</p>
                <p><strong>Predikat:</strong> {{ $data->predikat ?? '-' }}</p>
            </div>

            <!-- Tombol Finalisasi -->
            @if($data->status === 'Dinilai')
                <a href="{{ route('admin.skp.final', $data->id) }}"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow">
                    Finalisasi SKP
                </a>
            @endif
        </div>
    </div>
</x-app-layout>
