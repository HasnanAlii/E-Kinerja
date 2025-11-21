<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            SKP Saya
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

      
        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="mb-4 p-4 flex items-center bg-green-100 text-green-700 border border-green-300 rounded-lg text-base">
                <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 flex items-center bg-red-100 text-red-700 border border-red-300 rounded-lg text-base">
                <i data-feather="alert-circle" class="w-5 h-5 mr-3 text-red-500"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- CARD --}}
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl mt-12">

            {{-- HEADER CARD --}}
            <div class="px-6 py-5 border-b bg-gray-50 flex items-center justify-between rounded-t-lg">
                <div class="flex items-center gap-3">
                    <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                        <i data-feather="file-text" class="w-6 h-6"></i>
                    </span>

                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Daftar SKP Anda</h3>
                        <p class="text-base text-gray-500">Semua riwayat SKP berdasarkan periode yang Anda buat.</p>
                    </div>
                </div>

                {{-- BUTTON TAMBAH --}}
                <a href="{{ route('skp.create') }}"
                   class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition duration-150 ease-in-out text-base">
                    <i data-feather="plus" class="w-5 h-5 mr-2"></i>
                    Buat SKP Baru
                </a>
            </div>

            {{-- TABLE --}}
            <div class="bg-white border-b border-gray-200">
                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Periode
                                </th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($skp as $row)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    
                                    {{-- PERIODE --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-base text-gray-900">
                                            {{ $row->periode }}
                                        </span>
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 text-sm rounded-full
                                            @if($row->status=='Draft') bg-gray-100 text-gray-700
                                            @elseif($row->status=='Diajukan') bg-blue-100 text-blue-700
                                            @elseif($row->status=='Dinilai') bg-yellow-100 text-yellow-700
                                            @else bg-green-100 text-green-700
                                            @endif">
                                            {{ $row->status }}
                                        </span>
                                    </td>

                                    {{-- ACTION --}}
                                     <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center gap-3 justify-center">

                                            {{-- Tombol Detail --}}
                                            <a href="{{ route('skp.show', $row->id) }}"
                                            class="inline-flex items-center text-indigo-600 font-medium hover:text-indigo-800 transition">
                                                <i data-feather="eye" class="w-4 h-4 mr-1"></i>
                                                Detail
                                            </a>

                                            {{-- Tombol Ajukan (jika Draft) --}}
                                            @if($row->status == 'Draft')
                                                <form method="POST" action="{{ route('skp.ajukan', $row->id) }}">
                                                    @csrf
                                                    <button type="submit"
                                                      class="inline-flex items-center text-indigo-600 font-medium hover:text-indigo-800 transition">
                                                        <i data-feather="send" class="w-4 h-4 mr-1"></i>
                                                        Ajukan
                                                    </button>
                                                </form>
                                            @endif

                                        </div>

                                    </td>


                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-base text-gray-500">
                                        Belum ada SKP yang dibuat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>

                @if ($skp->hasPages())
                    <div class="mt-6 p-4 bg-gray-50 border-t rounded-b-lg">
                        {{ $skp->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>

                              
    