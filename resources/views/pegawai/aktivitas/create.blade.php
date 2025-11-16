<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Tambah Aktivitas Harian
        </h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow-xl sm:rounded-xl overflow-hidden border border-gray-200">

            {{-- HEADER --}}
            <div class="px-6 py-5 border-b bg-gray-50 flex items-center gap-3">
                <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                    <i data-feather="edit-3" class="w-6 h-6"></i>
                </span>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Form Tambah Aktivitas</h3>
                    <p class="text-base text-gray-500">Isi data aktivitas harian Anda dengan lengkap dan jelas.</p>
                </div>
            </div>

            <div class="p-6 sm:px-8">

                <form action="{{ route('pegawai.aktivitas.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- ROW 1 --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- TANGGAL --}}
                        <div>
                            <label for="tanggal" class="block text-base font-medium text-gray-700">
                                Tanggal Aktivitas
                            </label>
                            <input type="date" name="tanggal" id="tanggal"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-base"
                                   value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- WAKTU --}}
                        <div>
                            <label for="waktu_pelaksanaan" class="block text-base font-medium text-gray-700">
                                Waktu Pelaksanaan
                            </label>
                            <input type="text" name="waktu_pelaksanaan" id="waktu_pelaksanaan"
                                   placeholder="Contoh: 08.00 - 10.00"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-base"
                                   value="{{ old('waktu_pelaksanaan') }}" required>
                            @error('waktu_pelaksanaan')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- URAIAN --}}
                    <div class="mt-6">
                        <label for="uraian_tugas" class="block text-base font-medium text-gray-700">
                            Uraian Tugas
                        </label>
                        <textarea name="uraian_tugas" id="uraian_tugas" rows="4"
                                  placeholder="Tuliskan uraian tugas secara jelas dan detail..."
                                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-base"
                                  required>{{ old('uraian_tugas') }}</textarea>
                        @error('uraian_tugas')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- HASIL --}}
                    <div class="mt-6">
                        <label for="hasil_pekerjaan" class="block text-base font-medium text-gray-700">
                            Hasil Pekerjaan
                        </label>
                        <textarea name="hasil_pekerjaan" id="hasil_pekerjaan" rows="4"
                                  placeholder="Tuliskan hasil pekerjaan Anda..."
                                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-base">{{ old('hasil_pekerjaan') }}</textarea>
                        @error('hasil_pekerjaan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- BUKTI FILE --}}
                    <div class="mt-6">
                        <label for="bukti_file" class="block text-base font-medium text-gray-700">
                            Bukti File (PDF, JPG, PNG – Maks 2MB)
                        </label>
                        <input type="file" name="bukti_file" id="bukti_file"
                               class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 cursor-pointer text-base focus:outline-none file:bg-gray-100 file:px-4 file:py-2 file:rounded-md file:border-0 file:text-base file:font-medium file:text-gray-700">
                        @error('bukti_file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- BUTTONS --}}
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('pegawai.aktivitas.index') }}"
                           class="text-gray-600 hover:text-gray-900 mr-4 text-base">
                            Batal
                        </a>

                        <button type="submit"
                                class="inline-flex items-center bg-indigo-600 text-white px-5 py-2.5 rounded-lg shadow hover:bg-indigo-700 transition focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-base font-medium">
                            <i data-feather="save" class="w-5 h-5 mr-2"></i>
                            Simpan Aktivitas
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
