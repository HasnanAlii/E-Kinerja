<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Detail Penilaian Pegawai
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl sm:rounded-xl border border-gray-200">

                {{-- Header Card --}}
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center space-x-3 rounded-t-xl">
                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                        <i data-feather="clipboard" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">
                            Detail Penilaian Pegawai
                        </h3>
                        <p class="text-sm text-gray-500">
                            Informasi lengkap hasil penilaian pegawai untuk periode terkait.
                        </p>
                    </div>
                </div>

                {{-- Body --}}
                <div class="px-6 py-6 space-y-5">

                    {{-- Info Pegawai --}}
                    <div class="bg-gray-50 border rounded-lg p-4 flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <p class="text-base font-semibold text-gray-800">
                                {{ $data->pegawai->user->name }}
                            </p>
                            <p class="text-sm text-gray-600">
                                Bidang: {{ $data->pegawai->bidang->nama_bidang ?? '-' }}
                            </p>
                        </div>

                        <div class="mt-3 md:mt-0">
                            <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-lg text-sm font-medium">
                                {{ $data->periode->nama_periode }}
                            </span>
                        </div>
                    </div>

                    {{-- Tabel Penilaian --}}
                    <div class="overflow-hidden border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">

                                @foreach ([
                                    'skp' => 'SKP',
                                    'kedisiplinan' => 'Kedisiplinan',
                                    'perilaku' => 'Perilaku',
                                    'komunikasi' => 'Komunikasi',
                                    'tanggung_jawab' => 'Tanggung Jawab',
                                    'kerja_sama' => 'Kerja Sama',
                                    'produktivitas' => 'Produktivitas'
                                ] as $field => $label)

                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3 font-medium text-gray-700">{{ $label }}</td>
                                        <td class="px-6 py-3 text-gray-800">{{ $data->$field }}</td>
                                    </tr>

                                @endforeach

                                <tr class="bg-indigo-50 font-semibold">
                                    <td class="px-6 py-3 text-indigo-900">Nilai Total</td>
                                    <td class="px-6 py-3 text-indigo-900">
                                        {{ number_format($data->nilai_total, 2) }}
                                    </td>
                                </tr>

                                <tr class="bg-green-50 font-semibold">
                                    <td class="px-6 py-3 text-green-700">Kategori</td>
                                    <td class="px-6 py-3 text-green-700">{{ $data->kategori }}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex justify-end px-6 py-4 border-t bg-gray-50 rounded-b-xl">
                    <a href="{{ route('admin.penilaian.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold transition">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                        Kembali
                    </a>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
