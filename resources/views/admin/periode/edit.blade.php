<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Edit Periode Penilaian
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl sm:rounded-xl border border-gray-200">

                {{-- Header Card --}}
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center space-x-3 rounded-t-xl">
                    <div class="p-2 bg-yellow-100 text-yellow-600 rounded-lg">
                        <i data-feather="edit-3" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Form Edit Periode</h3>
                        <p class="text-sm text-gray-500">Perbarui informasi periode penilaian.</p>
                    </div>
                </div>

                {{-- FORM --}}
                <form action="{{ route('admin.periode.update', $data->id) }}" 
                      method="POST" class="px-6 pb-6 space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- NAMA PERIODE --}}
                    <div class="space-y-2">
                        <label class="font-medium text-gray-700">Nama Periode</label>
                        <input type="text" name="nama_periode"
                               value="{{ old('nama_periode', $data->nama_periode) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- TANGGAL --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        <div class="space-y-2">
                            <label class="font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai"
                                   value="{{ old('tgl_mulai', $data->tgl_mulai) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="space-y-2">
                            <label class="font-medium text-gray-700">Tanggal Selesai</label>
                            <input type="date" name="tgl_selesai"
                                   value="{{ old('tgl_selesai', $data->tgl_selesai) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.periode.index') }}" 
                           class="text-gray-600 hover:text-gray-900 mr-4 text-base">
                            Batal
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm text-base transition focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <i data-feather="save" class="w-5 h-5 mr-2"></i>
                            Perbarui Periode
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</x-app-layout>
