<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-gray-800 flex items-center gap-2">
            <i data-feather="clipboard" class="w-5 h-5 text-indigo-600"></i>
            Form Penilaian Pegawai
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                
                <div class="bg-indigo-400 px-6 py-8 sm:px-10 text-white">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="text-3xl font-bold tracking-tight">
                                {{ $pegawai->user->name }}
                            </h3>
                            <div class="mt-2 flex items-center gap-2 text-indigo-100 text-sm font-medium">
                                <i data-feather="user" class="w-4 h-4"></i>
                                <span>NIP/ID: {{ $pegawai->nip ?? '-' }}</span> </div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/20 text-right">
                            <p class="text-xs text-indigo-200 uppercase tracking-wider">Periode Penilaian</p>
                            <p class="text-lg font-bold">{{ $periode->nama_periode }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-10">
                    <div class="mb-8 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-md flex items-start gap-3">
                        <i data-feather="info" class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0"></i>
                        <div class="text-sm text-yellow-800">
                            <p class="font-bold mb-1">Panduan Penilaian:</p>
                            <p>Berikan penilaian objektif dengan skala <strong>1 (Sangat Buruk)</strong> hingga <strong>5 (Sangat Baik)</strong> pada setiap indikator kinerja di bawah ini.</p>
                        </div>
                    </div>

                    <form action="{{ route('atasan.penilaian.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                        <input type="hidden" name="periode_id" value="{{ $periode->id }}">

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

                        <div class="space-y-8">
                            @foreach ($indikator as $key => $label)
                                <div class="pb-6 border-b border-gray-100 last:border-0">
                                    <label class="block text-lg font-semibold text-gray-800 mb-4">
                                        {{ $loop->iteration }}. {{ $label }}
                                    </label>

                                    <div class="flex flex-wrap gap-3 sm:gap-4">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <div class="relative flex-1 min-w-[60px]">
                                                <input type="radio" 
                                                       name="{{ $key }}" 
                                                       id="{{ $key }}_{{ $i }}" 
                                                       value="{{ $i }}" 
                                                       class="peer sr-only" 
                                                       {{ old($key) == $i ? 'checked' : '' }}
                                                       required>
                                                
                                                <label for="{{ $key }}_{{ $i }}" 
                                                       class="flex flex-col items-center justify-center w-full p-3 text-gray-500 bg-white border-2 border-gray-200 rounded-xl cursor-pointer 
                                                              hover:bg-gray-50 hover:border-indigo-300 hover:text-indigo-600 transition-all duration-200
                                                              peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:font-bold shadow-sm">
                                                    <span class="text-xl">{{ $i }}</span>
                                                    
                                                    <span class="text-[10px] uppercase mt-1 text-center leading-tight">
                                                        @if($i == 1) Sangat Buruk
                                                        @elseif($i == 5) Sangat Baik
                                                        @else &nbsp;
                                                        @endif
                                                    </span>
                                                </label>
                                                
                                                <div class="absolute top-2 right-2 hidden peer-checked:block text-indigo-600">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    @error($key)
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                            <i data-feather="alert-circle" class="w-4 h-4"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-10 pt-6 border-t border-gray-200">
                            <a href="{{ route('atasan.penilaian.index') }}" 
                               class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                Batal
                            </a>
                            
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition transform active:scale-95">
                                <i data-feather="save" class="w-4 h-4 mr-2"></i>
                                Simpan Penilaian
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>