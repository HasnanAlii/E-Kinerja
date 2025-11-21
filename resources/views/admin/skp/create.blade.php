<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-indigo-700 flex items-center gap-2">
            <i data-feather="check-circle" class="w-5 h-5"></i>
            Finalisasi SKP Pegawai
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow border rounded-xl p-6">

                <p class="text-gray-800 text-lg mb-4">
                    Apakah Anda yakin ingin memfinalisasi SKP ini?
                </p>

                <p class="text-sm text-gray-600 mb-6">
                    Setelah finalisasi, data tidak bisa diubah lagi.
                </p>

                <form action="{{ route('admin.skp.final.store', $data->id) }}" method="POST">
                    @csrf

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.skp.show', $data->id) }}"
                           class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Batal
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow">
                            Finalisasi
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</x-app-layout>
