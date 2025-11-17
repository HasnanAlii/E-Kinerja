<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-gray-800 flex items-center gap-2">
            <i data-feather="file-text" class="w-5 h-5 text-indigo-600"></i>
            Detail Penilaian Pegawai
        </h2>
    </x-slot>

    <div class="py-5 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                
                <div class="bg-indigo-400 p-6 sm:p-10 text-white">
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <div class="flex-shrink-0">
                            @if( $data->pegawai->user->profile_photo)
                                <img class="h-24 w-24 rounded-full object-cover border-4 border-white/30 shadow-lg" 
                                     src="{{ asset('storage/' . ($data->pegawai->user->profile_photo ?? 'default.png')) }}"
                                     alt="{{ $data->pegawai->user->name }}" />
                            @else
                                <img class="h-24 w-24 rounded-full object-cover border-4 border-white/30 shadow-lg" 
                                     src="https://ui-avatars.com/api/?name={{ urlencode($data->pegawai->user->name) }}&color=7F9CF5&background=EBF4FF" 
                                     alt="{{ $data->pegawai->user->name }}" />
                            @endif
                        </div>

                        <div class="text-center sm:text-left flex-1">
                            <h3 class="text-3xl font-bold tracking-tight">
                                {{ $data->pegawai->user->name }}
                            </h3>
                            <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 text-indigo-100 text-sm font-medium">
                                <span class="flex items-center justify-center sm:justify-start gap-1">
                                    <i data-feather="briefcase" class="w-4 h-4"></i>
                                    {{ $data->pegawai->jabatan ?? 'Pegawai' }}
                                </span>
                                <span class="hidden sm:inline">•</span>
                                <span class="flex items-center justify-center sm:justify-start gap-1">
                                    <i data-feather="calendar" class="w-4 h-4"></i>
                                    Periode: {{ $data->periode->nama_periode }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 sm:mt-0 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/20 text-center">
                            <p class="text-xs text-indigo-200 uppercase tracking-wider">Dinilai Pada</p>
                            <p class="font-semibold">{{ $data->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-10">
                    
                    <h4 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2 border-gray-100">Rincian Kompetensi</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @php
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
                            @php
                                $nilai = $data->$key;
                                $persen = ($nilai / 5) * 100;
                                // Warna bar berdasarkan nilai
                                $colorClass = 'bg-indigo-600';
                                if($nilai <= 2) $colorClass = 'bg-red-500';
                                elseif($nilai <= 3) $colorClass = 'bg-yellow-500';
                                elseif($nilai <= 4) $colorClass = 'bg-blue-500';
                            @endphp

                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-sm transition-shadow">
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-sm font-medium text-gray-600">{{ $label }}</span>
                                    <span class="text-lg font-bold text-gray-800">{{ $nilai }} <span class="text-xs text-gray-400 font-normal">/5</span></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="{{ $colorClass }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10 bg-indigo-50 border border-indigo-100 rounded-2xl p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                            <div class="text-center sm:text-left">
                                <h5 class="text-indigo-900 font-semibold text-lg">Hasil Akhir Penilaian</h5>
                                <p class="text-indigo-600/80 text-sm mt-1">Akumulasi rata-rata dari seluruh indikator.</p>
                            </div>
                            
                            <div class="flex items-center gap-6">
                                <div class="text-center">
                                    <p class="text-xs text-indigo-500 uppercase font-bold mb-1">Total Skor</p>
                                    <div class="text-4xl font-extrabold text-indigo-700">
                                        {{ number_format($data->nilai_total, 2) }}
                                    </div>
                                </div>

                                <div class="h-12 w-px bg-indigo-200 hidden sm:block"></div>

                                <div class="text-center">
                                    <p class="text-xs text-indigo-500 uppercase font-bold mb-1">Predikat</p>
                                    @if($data->kategori == 'Sangat Baik')
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-green-100 text-green-700 border border-green-200">
                                            <i data-feather="star" class="w-3 h-3 mr-1.5 fill-current"></i> Sangat Baik
                                        </span>
                                    @elseif($data->kategori == 'Baik')
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                            <i data-feather="thumbs-up" class="w-3 h-3 mr-1.5"></i> Baik
                                        </span>
                                    @elseif($data->kategori == 'Cukup')
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                            <i data-feather="minus-circle" class="w-3 h-3 mr-1.5"></i> Cukup
                                        </span>
                                    @elseif($data->kategori == 'Kurang')
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-red-100 text-red-700 border border-red-200">
                                            <i data-feather="alert-triangle" class="w-3 h-3 mr-1.5"></i> Kurang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $data->kategori }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-start">
                        <a href="{{ route('atasan.laporan.index') }}" 
                           class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                            <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                            Kembali ke Laporan
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>