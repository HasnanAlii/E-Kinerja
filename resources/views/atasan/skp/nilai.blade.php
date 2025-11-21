<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
            Penilaian SKP Pegawai
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow border rounded-xl p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ $data->pegawai->user->name }}
                </h3>

                <form action="{{ route('atasan.skp.nilai.store', $data->id) }}" method="POST">
                    @csrf

                    {{-- Rating --}}
                    <label class="block text-gray-700 font-medium mb-1">Rating (0–100)</label>
                    <input type="number" name="rating" required min="0" max="100"
                           class="w-full border-gray-300 rounded-lg mb-4"
                           value="{{ old('rating', $data->rating) }}">

                    {{-- Predikat --}}
                    <label class="block text-gray-700 font-medium mb-1">Predikat</label>
                    <select name="predikat" class="w-full border-gray-300 rounded-lg mb-4">
                        <option value="Sangat Baik" {{ $data->predikat == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                        <option value="Baik" {{ $data->predikat == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Cukup" {{ $data->predikat == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                        <option value="Kurang" {{ $data->predikat == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                    </select>

                    <div class="flex justify-end gap-3 mt-4">
                        <a href="{{ route('atasan.skp.show', $data->id) }}"
                            class="px-4 py-2 bg-gray-100 rounded-lg text-gray-700 hover:bg-gray-200">
                            Batal
                        </a>

                        <button type="submit"
                            class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow">
                            Simpan Penilaian
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
