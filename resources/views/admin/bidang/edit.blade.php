<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Edit Bidang
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl sm:rounded-xl border border-gray-200">

                {{-- HEADER CARD --}}
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center space-x-3 rounded-t-xl">
                    <div class="p-2 bg-yellow-100 text-yellow-600 rounded-lg">
                        <i data-feather="edit" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Form Edit Bidang</h3>
                        <p class="text-sm text-gray-500">
                            Perbarui informasi nama bidang pada sistem.
                        </p>
                    </div>
                </div>

                <form action="{{ route('admin.bidang.update', $data->id) }}" method="POST" class="px-6 pb-6 space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- INPUT --}}
                    <div class="space-y-2">
                        <label class="text-base font-medium text-gray-700">Nama Bidang</label>
                        <input type="text" name="nama_bidang"
                               value="{{ old('nama_bidang', $data->nama_bidang) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base">
                        @error('nama_bidang')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- FOOTER BUTTON --}}
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200">

                        <a href="{{ route('admin.bidang.index') }}"
                           class="text-gray-600 hover:text-gray-900 mr-4 text-base">
                            Batal
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-700
                                       text-white rounded-lg shadow-sm text-base transition
                                       focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <i data-feather="save" class="w-5 h-5 mr-2"></i>
                            Perbarui Bidang
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
