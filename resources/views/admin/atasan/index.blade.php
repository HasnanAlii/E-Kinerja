<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Pegawai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class=" mx-auto sm:px-6 lg:px-8">
            
            {{-- Tabs --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <div class="border-b border-gray-200 mb-4 sm:mb-0">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">

                        {{-- Tab Pegawai --}}
                        <a href="{{ route('admin.pegawai.index') }}"
                           class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                            <i data-feather="users" class="w-5 h-5 mr-2"></i>
                            Daftar Pegawai
                        </a>
                        {{-- Tab Atasan (aktif) --}}
                        <a href="{{ route('admin.atasan.index') }}"
                           class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                            <i data-feather="user-check" class="w-5 h-5 mr-2"></i>
                            Daftar Atasan
                        </a>

                    </nav>
                </div>
           
            </div>
            
            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-4 p-4 flex items-center bg-green-100 text-green-700 border border-green-300 rounded-lg text-base">
                    <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Card --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                {{-- HEADER --}}
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center justify-between">
                    
                    {{-- LEFT --}}
                    <div class="flex items-center gap-3">
                        <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                            <i data-feather="user-check" class="w-6 h-6"></i>
                        </span>

                        <div>
                            <h3 class="text-lg font-bold text-gray-800">
                                Manajemen Atasan
                            </h3>
                            <p class="text-base text-gray-500">
                                Kelola data atasan beserta unit kerja dan relasi pegawai terkait.
                            </p>
                        </div>
                    </div>

                    {{-- BUTTON --}}
                    <a href="{{ route('admin.atasan.create') }}"
                    class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-base">
                        <i data-feather="user-plus" class="w-5 h-5 mr-2 -ml-1"></i>
                        Tambah Atasan
                    </a>

                </div>


                <div class="p-6 sm:px-8 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">NO</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">NAMA</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">BIDANG</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">JABATAN</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @forelse ($atasan as $i => $a)
                                <tr class="hover:bg-gray-50 transition">

                                    {{-- No --}}
                                    <td class="px-6 py-4 text-base text-gray-700">{{ $i + 1 }}</td>

                                    {{-- Atasan (Foto + Nama) --}}
                                    <td class="px-6 py-4 whitespace-nowrap flex items-center space-x-3">

                                        {{-- Foto Atasan --}}
                                        @if ($a->foto)
                                            <img src="{{ asset('storage/' . $a->foto) }}"
                                                 class="w-12 h-12 rounded-full object-cover border">
                                        @else
                                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center border">
                                                <i data-feather="user" class="text-gray-500 w-6 h-6"></i>
                                            </div>
                                        @endif

                                        {{-- Nama --}}
                                        <div class="text-base font-medium text-gray-900">
                                            {{ $a->user->name }}
                                        </div>
                                    </td>

                                            <td class="px-6 py-4 text-base text-gray-700">
                                        {{ $a->user->email }}
                                    </td>

                                    {{-- Bidang --}}
                                    <td class="px-6 py-4 text-base text-gray-700">
                                        {{ $a->bidang->nama_bidang }}
                                    </td>

                                    {{-- NIP --}}
                                    <td class="px-6 py-4 text-base text-gray-700">
                                        {{ $a->nip ?? '-' }}
                                    </td>

                                    {{-- Jabatan --}}
                                    <td class="px-6 py-4 text-base text-gray-700">
                                        {{ $a->jabatan ?? '-' }}
                                    </td>


                                    {{-- Aksi --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-base font-medium">
                                        <div class="flex items-center space-x-4">
                                            
                                            {{-- Edit --}}
                                            <a href="{{ route('admin.atasan.edit', $a->id) }}"
                                                class="inline-flex items-center text-yellow-600 hover:text-yellow-900">
                                                <i data-feather="edit" class="w-4 h-4 mr-1"></i>
                                                Edit
                                            </a>

                                            {{-- Delete --}}
                                            <form action="{{ route('admin.atasan.destroy', $a->id) }}" 
                                                  method="POST"
                                                  onsubmit="return confirm('Hapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button
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
                                    <td colspan="7" class="px-6 py-6 text-center text-base text-gray-500">
                                        Belum ada data atasan.
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
