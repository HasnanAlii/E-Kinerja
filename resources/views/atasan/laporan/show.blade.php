<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detail Penilaian Pegawai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-8 bg-white">

                    <h3 class="text-2xl font-semibold leading-7 text-gray-900">
                        {{ $data->pegawai->user->name }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-base leading-6 text-gray-600">
                        Periode Penilaian: <strong>{{ $data->periode->nama_periode }}</strong>
                    </p>

                    <div class="mt-6 border-t border-gray-200">
                        <dl class="divide-y divide-gray-200">
                            @php
                                // Array untuk looping yang bersih
                                $indikator = [
                                    'skp' => 'Capaian SKP',
                                    'kedisiplinan' => 'Kedisiplinan',
                                    'perilaku' => 'Perilaku',
                                    'komunikasi' => 'Komunikasi',
                                    'tanggung_jawab' => 'Tanggung Jawab',
                                    'kerja_sama' => 'Kerja Sama',
                                    'produktivitas' => 'Produktivitas'
                                ];
                            @endphp

                            @foreach ($indikator as $key => $label)
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-base font-medium text-gray-500">{{ $label }}</dt>
                                    <dd class="mt-1 text-base text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $data->$key }} / 5
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dl class="space-y-4">
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-lg font-bold text-gray-700">Nilai Akhir</dt>
                                <dd class="mt-1 text-2xl font-bold text-indigo-600 sm:mt-0 sm:col-span-2">
                                    {{ number_format($data->nilai_total, 2) }}
                                </dd>
                            </div>
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-lg font-bold text-gray-700">Kategori</dt>
                                <dd class="mt-1 sm:mt-0 sm:col-span-2">
                                    @if($data->kategori == 'Sangat Baik')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Sangat Baik
                                        </span>
                                    @elseif($data->kategori == 'Baik')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Baik
                                        </span>
                                    @elseif($data->kategori == 'Cukup')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Cukup
                                        </span>
                                    @elseif($data->kategori == 'Kurang')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Kurang
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 capitalize">
                                            {{ $data->kategori }}
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('atasan.laporan.index') }}" 
                           class="inline-flex items-center text-base text-gray-600 hover:text-gray-900 font-medium">
                            <i data-feather="arrow-left" class="w-5 h-5 mr-2"></i>
                            Kembali ke Laporan
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>