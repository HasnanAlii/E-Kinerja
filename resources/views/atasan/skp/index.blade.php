<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <i data-feather="users" class="w-5 h-5 text-indigo-600"></i>
            Daftar SKP Bawahan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="mb-6 p-4 flex items-center bg-green-50 text-green-700 border border-green-200 rounded-xl shadow-sm">
                    <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- CARD UTAMA --}}
            <div class="bg-white shadow-xl sm:rounded-2xl border border-gray-100 overflow-hidden">

                {{-- HEADER CARD --}}
                <div class="px-8 py-6 border-b border-gray-100 bg-white flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl shadow-sm ring-1 ring-indigo-100">
                            <i data-feather="clipboard" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Daftar SKP Bawahan</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Kelola dan nilai kinerja pegawai di bawah supervisi Anda.</p>
                        </div>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pegawai</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Periode</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($skp as $item)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-150">

                                    {{-- NAMA PEGAWAI --}}
                               <td class="px-6 py-4 flex items-center gap-3 whitespace-nowrap">
                                        @if ($item->pegawai->user->profile_photo)
                                            <img 
                                                src="{{ asset('storage/' . $item->pegawai->user->profile_photo) }}"
                                                class="h-10 w-10 rounded-full object-cover border shadow-sm"
                                                alt="Foto Profil"
                                            >
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center border shadow-sm">
                                                <div class="w-full h-full rounded-full bg-indigo-50 flex items-center justify-center text-indigo-300">
                                                    <i data-feather="user" class="w-5 h-5"></i>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="flex flex-col">
                                            <span class="text-base font-semibold text-gray-900">
                                                {{ $item->pegawai->user->name }}
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                {{ $item->pegawai->jabatan }}
                                            </span>
                                        </div>
                                    </td>
                                    {{-- PERIODE --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600 font-medium bg-gray-100 px-2.5 py-1 rounded-md">
                                            {{ $item->periode }}
                                        </span>
                                    </td>

                                    {{-- STATUS BADGE --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $statusClasses = [
                                                'Draft'    => 'bg-gray-100 text-gray-600 border-gray-200',
                                                'Diajukan' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'Disetujui'  => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                'Selesai'  => 'bg-green-50 text-green-700 border-green-200',
                                            ];
                                            $class = $statusClasses[$item->status] ?? 'bg-gray-100 text-gray-600';
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $class }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            
                                            {{-- Detail --}}
                                            <a href="{{ route('atasan.skp.show', $item->id) }}"
                                               class="group inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">
                                                <i data-feather="eye" class="w-4 h-4 mr-1.5 group-hover:scale-110 transition-transform"></i>
                                                Detail
                                            </a>

                                            <span class="text-gray-300">|</span>

                                            {{-- Update Status --}}
                                            <button onclick="openModal('{{ $item->id }}')"
                                                    class="group inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">
                                                <i data-feather="edit-2" class="w-4 h-4 mr-1.5 group-hover:scale-110 transition-transform"></i>
                                                Status
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-50 p-4 rounded-full mb-3">
                                                <i data-feather="inbox" class="w-8 h-8 text-gray-300"></i>
                                            </div>
                                            <p class="text-gray-500 text-sm font-medium">Belum ada SKP bawahan yang diajukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if ($skp->hasPages())
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-200">
                        {{ $skp->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- MODAL LOOP --}}
    @foreach($skp as $item)
        <div id="modal-{{ $item->id }}" 
             class="hidden fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('{{ $item->id }}')"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i data-feather="refresh-cw" class="h-5 w-5 text-indigo-600"></i>
                            </div>
                            
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-bold leading-6 text-gray-900" id="modal-title">Update Status SKP</h3>
                                
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('atasan.skp.updateStatus', $item->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="space-y-4">
                                            {{-- Select Status --}}
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tindakan</label>
                                                <select name="status" id="statusSelect{{ $item->id }}" 
                                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition text-sm"
                                                    onchange="toggleCommentField('{{ $item->id }}')">
                                                    <option value="Disetujui" @selected($item->status == 'Disetujui')>Setujui / Lanjut Penilaian</option>
                                                    <option value="Draft" @selected($item->status == 'Draft')>Revisi (Kembalikan ke Pegawai)</option>
                                                </select>
                                            </div>

                                            {{-- Kolom Komentar Atasan --}}
                                            <div id="commentField{{ $item->id }}" class="{{ $item->status == 'Draft' ? '' : 'hidden' }}">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Revisi</label>
                                                <textarea name="komentar_atasan" rows="3"
                                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    placeholder="Berikan catatan perbaikan untuk pegawai..."></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end gap-3">
                                            <button type="button" onclick="closeModal('{{ $item->id }}')"
                                                class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- SCRIPT JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        function openModal(id) {
            document.getElementById("modal-" + id).classList.remove("hidden");
            // Panggil toggle saat buka modal untuk set state awal kolom komentar
            toggleCommentField(id);
        }

        function closeModal(id) {
            document.getElementById("modal-" + id).classList.add("hidden");
        }

        function toggleCommentField(id) {
            const status = document.getElementById('statusSelect' + id).value;
            const commentField = document.getElementById('commentField' + id);

            if (status === 'Draft') {
                commentField.classList.remove('hidden');
            } else {
                commentField.classList.add('hidden');
            }
        }
    </script>

</x-app-layout>