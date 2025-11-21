<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detail Aktivitas Pegawai
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-start">
             <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-gray-700 font-medium flex items-center transition mb-4">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                                Kembali
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                
                <div class="p-6 sm:px-8 bg-white">

                    {{-- HEADER --}}
                    <div>
                        
                        <h3 class="text-2xl font-semibold text-gray-900">
                            {{ $data->pegawai->user->name }}
                        </h3>
                        <p class="mt-1 text-base text-gray-500">Detail laporan aktivitas harian.</p>
                    </div>
                    {{-- DETAIL --}}
                    <div class="mt-6 border-t border-gray-200">
                        <dl class="divide-y divide-gray-200">

                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                                <dd class="text-sm text-gray-900 sm:col-span-2">
                                    {{ \Carbon\Carbon::parse($data->tanggal)->isoFormat('D MMMM YYYY') }}
                                </dd>
                            </div>

                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Waktu Pelaksanaan</dt>
                                <dd class="text-sm text-gray-900 sm:col-span-2">
                                    {{ $data->waktu_pelaksanaan }}
                                </dd>
                            </div>

{{-- Uraian Tugas --}}
<div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
    <dt class="text-sm font-medium text-gray-500">Uraian Tugas</dt>
    <dd class="sm:col-span-2">
        <div class="p-3 rounded-lg border bg-gray-50 text-sm text-gray-900 whitespace-pre-wrap">
            {{ $data->uraian_tugas }}
        </div>
    </dd>
</div>

{{-- Hasil Pekerjaan --}}
<div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
    <dt class="text-sm font-medium text-gray-500">Hasil Pekerjaan</dt>
    <dd class="sm:col-span-2">
        <div class="p-3 rounded-lg border bg-gray-50 text-sm text-gray-900 whitespace-pre-wrap">
            {{ $data->hasil_pekerjaan ?? '-' }}
        </div>
    </dd>
</div>



                            @if ($data->bukti_file)
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Bukti File</dt>
                                    <dd class="text-sm sm:col-span-2">
                                        <a href="{{ Storage::url($data->bukti_file) }}" target="_blank"
                                           class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium">
                                            <i data-feather="external-link" class="w-4 h-4 mr-2"></i>
                                            Lihat Bukti
                                        </a>
                                    </dd>
                                </div>
                            @endif

                        </dl>
                    </div>


                    @if ($data->status !== 'menunggu')
                        <div class="mt-8 p-5 bg-gray-50 border rounded-xl shadow-sm">

                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Hasil Verifikasi Atasan</h3>

                            {{-- STATUS BADGE --}}
                            @php
                                $badge = [
                                    'disetujui' => 'bg-green-100 text-green-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                    'revisi' => 'bg-yellow-100 text-yellow-700',
                                    'menunggu' => 'bg-gray-100 text-gray-700'
                                ];
                            @endphp

                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500 mb-1">Status</p>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $badge[$data->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($data->status) }}
                                </span>
                            </div>

                            {{-- KOMENTAR ATASAN --}}
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500 mb-1">Catatan Atasan</p>
                                <div class="p-3 bg-white border rounded-lg text-gray-800 whitespace-pre-wrap">
                                    {{ $data->komentar_atasan ?? '-' }}
                                </div>
                            </div>

                            {{-- TANGGAL VERIFIKASI OPTIONAL (kalau ingin) --}}
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Tanggal Verifikasi</p>
                                <p class="text-gray-800">
                                    {{ $data->updated_at->isoFormat('D MMMM YYYY HH:mm') }}
                                </p>
                            </div>

                        </div>
                    @endif

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Tindakan Verifikasi</h3>
                        <p class="mt-1 text-sm text-gray-500">Pilih tindakan yang ingin dilakukan.</p>

                        <div class="flex flex-col sm:flex-row gap-3 mt-6">

                            {{-- SETUJUI --}}
                            <button onclick="approveAction()"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 rounded-xl text-white bg-green-600 hover:bg-green-700 shadow-lg transform hover:scale-[1.02] transition-all">
                                <i data-feather="check" class="w-5 h-5 mr-2"></i>
                                Setujui
                            </button>

                            {{-- TOLAK --}}
                            <button onclick="rejectAction()"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 rounded-xl text-white bg-red-600 hover:bg-red-700 shadow-lg transform hover:scale-[1.02] transition-all">
                                <i data-feather="x" class="w-5 h-5 mr-2"></i>
                                Tolak
                            </button>

                            {{-- REVISI --}}
                            <button onclick="revisiAction()"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 rounded-xl text-white bg-yellow-500 hover:bg-yellow-600 shadow-lg transform hover:scale-[1.02] transition-all">
                                <i data-feather="edit" class="w-5 h-5 mr-2"></i>
                                Revisi
                            </button>

                        </div>

                        {{-- Hidden form --}}
                        <form id="verifikasiForm" action="" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="komentar_atasan" id="komentarInput">
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- SWEETALERT2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function approveAction() {
            Swal.fire({
                title: "Setujui Aktivitas?",
                text: "Anda yakin ingin menyetujui aktivitas ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Setujui",
                cancelButtonText: "Batal",
                confirmButtonColor: "#16a34a",
                cancelButtonColor: "#6b7280",
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('verifikasiForm');
                    form.action = "{{ route('atasan.verifikasi.approve', $data->id) }}";
                    form.submit();
                }
            });
        }

        function rejectAction() {
            Swal.fire({
                title: "Tolak Aktivitas",
                input: "textarea",
                inputLabel: "Alasan Penolakan (Wajib diisi)",
                inputPlaceholder: "Masukan alasan",
                inputAttributes: { "aria-label": "Alasan penolakan" },
                showCancelButton: true,
                confirmButtonText: "Tolak",
                cancelButtonText: "Batal",
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#6b7280",
                preConfirm: (value) => {
                    if (!value) {
                        Swal.showValidationMessage("Alasan wajib diisi!");
                    }
                    return value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('komentarInput').value = result.value;
                    const form = document.getElementById('verifikasiForm');
                    form.action = "{{ route('atasan.verifikasi.reject', $data->id) }}";
                    form.submit();
                }
            });
        }

        function revisiAction() {
            Swal.fire({
                title: "Minta Revisi",
                input: "textarea",
                inputLabel: "Catatan Revisi (Wajib diisi)",
                inputPlaceholder: "Masukan Revisi",
                showCancelButton: true,
                confirmButtonText: "Kirim Revisi",
                cancelButtonText: "Batal",
                confirmButtonColor: "#f59e0b",
                cancelButtonColor: "#6b7280",
                preConfirm: (value) => {
                    if (!value) {
                        Swal.showValidationMessage("Catatan revisi wajib diisi!");
                    }
                    return value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('komentarInput').value = result.value;
                    const form = document.getElementById('verifikasiForm');
                    form.action = "{{ route('atasan.verifikasi.revisi', $data->id) }}";
                    form.submit();
                }
            });
        }
    </script>

</x-app-layout>