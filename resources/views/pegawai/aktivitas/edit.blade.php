<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Edit Aktivitas Harian
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-8 bg-white">
                    
                    <form action="{{ route('pegawai.aktivitas.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-1">
                                <label for="tanggal" class="block text-base font-medium text-gray-700">
                                    Tanggal
                                </label>
                                <input type="date" name="tanggal" id="tanggal" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" 
                                       value="{{ old('tanggal', $data->tanggal) }}" 
                                       required>
                                @error('tanggal')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-1">
                                <label for="waktu_pelaksanaan" class="block text-base font-medium text-gray-700">
                                    Waktu Pelaksanaan
                                </label>
                                <input type="text" name="waktu_pelaksanaan" id="waktu_pelaksanaan" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" 
                                       value="{{ old('waktu_pelaksanaan', $data->waktu_pelaksanaan) }}" 
                                       required>
                                @error('waktu_pelaksanaan')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="uraian_tugas" class="block text-base font-medium text-gray-700">
                                Uraian Tugas
                            </label>
                            <textarea name="uraian_tugas" id="uraian_tugas" rows="4" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" 
                                      required>{{ old('uraian_tugas', $data->uraian_tugas) }}</textarea>
                            @error('uraian_tugas')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="hasil_pekerjaan" class="block text-base font-medium text-gray-700">
                                Hasil Pekerjaan
                            </label>
                            <textarea name="hasil_pekerjaan" id="hasil_pekerjaan" rows="4" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base">{{ old('hasil_pekerjaan', $data->hasil_pekerjaan) }}</textarea>
                            @error('hasil_pekerjaan')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="bukti_file" class="block text-base font-medium text-gray-700">
                                Ganti Bukti File (Opsional)
                            </label>

                            @if ($data->bukti_file)
                                <div class="mt-2 text-base text-gray-600">
                                    <i data-feather="file-text" class="w-4 h-4 inline-block mr-1"></i>
                                    File saat ini: 
                                    <a href="{{ Storage::url($data->bukti_file) }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">
                                        Lihat Bukti
                                    </a>
                                    <span class="text-gray-400 italic ml-2">(Biarkan kosong jika tidak ingin ganti)</span>
                                </div>
                            @else
                                <p class="mt-2 text-base text-gray-500 italic">Belum ada bukti file.</p>
                            @endif

                            <input type="file" name="bukti_file" id="bukti_file" 
                                   class="mt-2 block w-full text-base text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-4 file:py-2 file:mr-4 file:text-base file:font-medium">
                            @error('bukti_file')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('pegawai.aktivitas.index') }}" 
                               class="text-gray-600 hover:text-gray-900 mr-4 text-base">
                                Batal
                            </a>
                            
                            <button type="submit" 
                                    class="inline-flex items-center bg-green-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-green-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-base">
                                <i data-feather="save" class="w-5 h-5 mr-2 -ml-1"></i>
                                Simpan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>