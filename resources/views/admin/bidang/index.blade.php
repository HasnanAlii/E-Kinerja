<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Manajemen Bidang
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">


            @if (session('success'))
                <div class="mb-4 p-4 flex items-center bg-green-100 text-green-700 border border-green-300 rounded-lg text-base">
                    <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    {{-- HEADER --}}
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center justify-between">
                    
                    {{-- LEFT --}}
                    <div class="flex items-center gap-3">
                        <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                            <i data-feather="folder" class="w-6 h-6"></i>
                        </span>

                        <div>
                            <h3 class="text-lg font-bold text-gray-800">
                                Manajemen Bidang
                            </h3>
                            <p class="text-base text-gray-500">
                                Kelola data bidang dalam struktur organisasi Dinas Arsip & Perpustakaan.
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('admin.bidang.create') }}"
                        class="inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-2.5 rounded-xl shadow-md shadow-indigo-200 hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-300 transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 text-sm font-medium">
                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                        Tambah Bidang
                    </a>


                </div>

                <div class=" bg-white">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left pl-10 text-sm font-medium text-gray-500 uppercase tracking-wider">Nama Bidang</th>
                                    <th scope="col" class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($data as $row)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap pl-10 ">
                                            <div class="text-base font-medium text-gray-900">{{ $row->nama_bidang }}</div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-center">
                                            <div class="flex items-center justify-center space-x-4">
                                                <a href="{{ route('admin.bidang.edit', $row->id) }}"
                                                class="inline-flex items-center text-yellow-600 hover:text-yellow-900">
                                                    <i data-feather="edit" class="w-4 h-4 mr-1"></i>
                                                    Edit
                                                </a>

                                                <form action="{{ route('admin.bidang.destroy', $row->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus bidang ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center text-red-600 hover:text-red-900">
                                                        <i data-feather="trash-2" class="w-4 h-4 mr-1"></i>
                                                        Hapus
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-6 text-center text-base text-gray-500">
                                            Belum ada data bidang.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($data->hasPages())
                        <div class="mt-6 p-4 bg-gray-50 border-t rounded-b-lg">
                            {{ $data->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>