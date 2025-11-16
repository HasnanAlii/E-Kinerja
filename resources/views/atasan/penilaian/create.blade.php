<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Form Penilaian Pegawai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-8 bg-white">

                    <h3 class="text-2xl font-semibold leading-7 text-gray-900">
                        {{ $pegawai->user->name }}
                    </h3>
                    <p class="mt-1 text-base leading-6 text-gray-600">
                        Periode Penilaian: <strong>{{ $periode->nama_periode }}</strong>
                    </p>
                    <p class="mt-2 text-sm text-gray-500 italic">
                        Skala Penilaian: 1 (Sangat Buruk) s/d 5 (Sangat Baik)
                    </p>

                    <form action="{{ route('atasan.penilaian.store') }}" method="POST" class="mt-6">
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

                        <div class="space-y-6">
                            @foreach ($indikator as $key => $label)
                                <div>
                                    <label for="{{ $key }}" class="block text-base font-medium text-gray-700">
                                        {{ $label }}
                                    </label>
                                    <select name="{{ $key }}" id="{{ $key }}" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" 
                                            required>
                                        <option value="" disabled {{ old($key) ? '' : 'selected' }}>-- Pilih Nilai --</option>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ old($key) == $i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error($key)
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('atasan.penilaian.index') }}" 
                               class="text-gray-600 hover:text-gray-900 mr-4 text-base">
                                Batal
                            </a>
                            
                            <button type="submit" 
                                    class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-base">
                                <i data-feather="save" class="w-5 h-5 mr-2 -ml-1"></i>
                                Simpan Penilaian
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>