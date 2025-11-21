<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Daftar Aktivitas Harian
        </h2>
    </x-slot>

    <div class="py-12">
        <div class=" mx-auto sm:px-6 lg:px-8">
   

            @if (session('success'))
                <div class="mb-4 p-4 flex items-center bg-green-100 text-green-700 border border-green-300 rounded-lg">
                    <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-5 border-b bg-gray-50 flex items-center justify-between  rounded-t-lg">
                <div class="flex items-center gap-3">
                    <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                        <i data-feather="list" class="w-6 h-6"></i>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">
                            Riwayat Aktivitas Harian
                        </h3>
                        <p class="text-base text-gray-500">
                            Lihat, kelola, dan pantau aktivitas harian yang telah Anda laporkan.
                        </p>
                    </div>
                </div>
                    <div class="flex gap-4  justify-end">
                        <a href="{{ route('pegawai.aktivitas.create') }}" 
                        class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <i data-feather="plus" class="w-5 h-5 mr-2 -ml-1"></i>
                            Tambah Aktivitas
                        </a>
                    </div>
            </div>

                <div class=" bg-white border-b border-gray-200">
                    
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Uraian
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Komentar Atasan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($data as $row)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-base text-gray-900">
                                                {{-- Format tanggal agar lebih rapi --}}
                                                {{ \Carbon\Carbon::parse($row->tanggal)->isoFormat('D MMMM YYYY') }}
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <div class="text-base text-gray-700 whitespace-normal">
                                                {{ $row->uraian_tugas }}
                                            </div>
                                        </td>
                        
                                        <td class="px-6 py-4 text-base text-gray-700 text-center">
                                            <button onclick="openCommentModal('{{ $row->id }}')"
                                                class="text-blue-600 hover:text-blue-800 underline">
                                                Lihat Komentar
                                            </button>
                                        </td>

                                        
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($row->status == 'menunggu')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Menunggu
                                                </span>
                                            @elseif($row->status == 'disetujui')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Disetujui
                                                </span>
                                            @elseif($row->status == 'ditolak')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Ditolak
                                                </span>
                                            @else
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 capitalize">
                                                    {{ $row->status }}
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-center">
                                            <div class="flex items-center space-x-4 justify-center">
                                                @if ($row->status == 'menunggu')
                                                    <a href="{{ route('pegawai.aktivitas.edit', $row->id) }}" 
                                                       class="flex items-center text-indigo-600 hover:text-indigo-900" 
                                                       title="Edit">
                                                       <i data-feather="edit-2" class="w-4 h-4 mr-1"></i> Edit
                                                    </a>
                                                    
                                                    <form method="POST" action="{{ route('pegawai.aktivitas.destroy', $row->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aktivitas ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="flex items-center text-red-600 hover:text-red-900" 
                                                                title="Hapus">
                                                            <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="flex items-center text-gray-400 cursor-not-allowed" title="Terkunci">
                                                        <i data-feather="lock" class="w-4 h-4 mr-1"></i> Terkunci
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-base text-gray-500">
                                            Belum ada data aktivitas harian.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    <script>
                        const comments = @json($data->pluck('komentar_atasan', 'id'));

                        function openCommentModal(id) {
                            document.getElementById('komentarText').textContent =
                                comments[id] ? comments[id] : 'Tidak ada komentar';

                            document.getElementById('commentModal').classList.remove('hidden');
                            document.getElementById('commentModal').classList.add('flex');
                        }

                        function closeCommentModal() {
                            document.getElementById('commentModal').classList.add('hidden');
                            document.getElementById('commentModal').classList.remove('flex');
                        }
                    </script>

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
    <div id="commentModal" 
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity duration-300">
        
        <div class="relative w-full max-w-lg scale-100 transform overflow-hidden rounded-2xl bg-white p-6 shadow-2xl transition-all sm:p-8">
            
            <div class="mb-6 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Komentar Atasan</h2>
                    <p class="text-sm text-slate-500">Catatan penilaian kinerja</p>
                </div>
            </div>

            <div class="mb-8 rounded-xl border border-blue-100 bg-slate-50 p-5">
                <p id="komentarText" class="whitespace-pre-line text-base leading-relaxed text-slate-700">
                    </p>
            </div>

            <div class="flex justify-end">
                <button onclick="closeCommentModal()" 
                        class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 py-3 px-6 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 transition-all hover:from-blue-700 hover:to-blue-800 hover:shadow-blue-500/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
                    Tutup Penilaian
                </button>
            </div>
            
            <button onclick="closeCommentModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</x-app-layout>
