<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Ajukan Izin / Sakit
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-8 bg-white">
                    
                    <form action="{{ route('pegawai.izin.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <label for="jenis" class="block text-base font-medium text-gray-700">
                                Jenis Pengajuan
                            </label>
                            <select name="jenis" id="jenis" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" 
                                    required>
                                <option value="" disabled {{ old('jenis') ? '' : 'selected' }}>-- Pilih Jenis --</option>
                                <option value="izin" {{ old('jenis') == 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="sakit" {{ old('jenis') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            </select>
                            @error('jenis')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="tanggal_mulai" class="block text-base font-medium text-gray-700">
                                    Tanggal Mulai
                                </label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" 
                                       value="{{ old('tanggal_mulai', date('Y-m-d')) }}" 
                                       required>
                                @error('tanggal_mulai')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_selesai" class="block text-base font-medium text-gray-700">
                                    Tanggal Selesai
                                </label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" 
                                       value="{{ old('tanggal_selesai', date('Y-m-d')) }}" 
                                       required>
                                @error('tanggal_selesai')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="file_surat" class="block text-base font-medium text-gray-700">
                                Lampiran File (Opsional - PDF, JPG, PNG, maks 2MB)
                            </label>
                            <input type="file" name="file_surat" id="file_surat" 
                                   class="mt-1 block w-full text-base text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-4 file:py-2 file:mr-4 file:text-base file:font-medium">
                            @error('file_surat')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('pegawai.izin.index') }}" 
                               class="text-gray-600 hover:text-gray-900 mr-4 text-base">
                                Batal
                            </a>
                            
                            <button type="submit" 
                                    class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-base">
                                <i data-feather="send" class="w-5 h-5 mr-2 -ml-1"></i>
                                Kirim Pengajuan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>