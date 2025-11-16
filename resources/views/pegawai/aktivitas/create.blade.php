<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Tambah Aktivitas Harian
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-8 bg-white">
                    
                    <form action="{{ route('pegawai.aktivitas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-1">
                                <label for="tanggal" class="block text-base font-medium text-gray-700">
                                    Tanggal
                                </label>
                                <input type="date" name="tanggal" id="tanggal" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" 
                                       value="{{ old('tanggal', date('Y-m-d')) }}" 
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
                                       value="{{ old('waktu_pelaksanaan') }}" 
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
                                      required>{{ old('uraian_tugas') }}</textarea>
                            @error('uraian_tugas')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="hasil_pekerjaan" class="block text-base font-medium text-gray-700">
                                Hasil Pekerjaan
                            </label>
                            <textarea name="hasil_pekerjaan" id="hasil_pekerjaan" rows="4" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base">{{ old('hasil_pekerjaan') }}</textarea>
                            @error('hasil_pekerjaan')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="bukti_file" class="block text-base font-medium text-gray-700">
                                Bukti File (Opsional - PDF, JPG, PNG, maks 2MB)
                            </label>
                            <input type="file" name="bukti_file" id="bukti_file" 
                                   class="mt-1 block w-full text-base text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-4 file:py-2 file:mr-4 file:text-base file:font-medium">
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
                                    class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-base">
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