{{-- resources/views/dashboard/admin.blade.php --}}
<x-app-layout>
    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="mx-auto sm:px-6 lg:px-8">

            {{-- ALERT --}}
            @if(session('welcome') || true)
                <div id="welcomeAlert" class="mb-6">
                    <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 text-blue-800 px-5 py-4 rounded-xl shadow-sm relative">

                        <div class="flex-shrink-0">
                            <i data-feather="info" class="w-5 h-5 text-blue-600"></i>
                        </div>

                        <div class="flex-1">
                            <p class="font-semibold text-blue-900">Selamat datang, {{ Auth::user()->name }}! 🎉</p>
                            <p class="text-sm text-blue-700 mt-0.5">
                                Semoga hari Anda menyenangkan. Berikut ringkasan kinerja hari ini.
                            </p>
                        </div>

                        <button onclick="document.getElementById('welcomeAlert').remove()" 
                            class="absolute top-3 right-3 text-blue-500 hover:text-blue-700 transition">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>

                    </div>
                </div>
            @endif


            {{-- HEADER CARD --}}
            <div class="bg-blue-600 rounded-2xl p-6 sm:p-10 text-white shadow-xl shadow-blue-200 mb-10 relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold">Dashboard Pegawai</h1>
                    <p class="mt-2 text-blue-100 max-w-xl">
                        Ringkasan aktivitas dan progres kerjamu ditampilkan di sini.
                    </p>
                </div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            </div>


            {{-- GRID UTAMA: KONTEN + QUICK ACTION --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                {{-- ============================
                     KOLOM KIRI (Konten Utama)
                ============================ --}}
                <div class="lg:col-span-2">

                    {{-- STATISTIK SINGKAT --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                        {{-- Kehadiran Bulan Ini --}}
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Kehadiran Bulan Ini</p>
                                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $data['kehadiran_bulan_ini'] }}</p>
                                </div>
                                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                                    <i data-feather="clock" class="w-6 h-6"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Total Laporan --}}
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Laporan</p>
                                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $data['total_laporan'] }}</p>
                                </div>
                                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                                    <i data-feather="file-text" class="w-6 h-6"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Nilai Terakhir --}}
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Nilai Terakhir</p>
                                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $data['nilai_terakhir'] }}</p>
                                </div>
                                <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                                    <i data-feather="award" class="w-6 h-6"></i>
                                </div>
                            </div>
                        </div>

                    </div>


                    {{-- AKTIVITAS TERBARU --}}
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4">Aktivitas Terbaru</h3>

                        @if($data['aktivitas']->count() > 0)
                            <div class="space-y-4">

                                @foreach ($data['aktivitas'] as $act)
                                             <div class="group flex items-start gap-4 p-4 rounded-xl border border-gray-100 
                                        bg-gray-50 hover:bg-white hover:border-indigo-200 hover:shadow-md 
                                        transition-all duration-200">

                                        <div class="flex-shrink-0">
                                            @if($act->pegawai->user->profile_photo)
                                                <img src="{{ asset('storage/' . $act->pegawai->user->profile_photo) }}"
                                                    class="h-14 w-14 rounded-full object-cover border-2 border-white shadow-sm">
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center 
                                                            text-indigo-600 font-bold text-sm border-2 border-white shadow-sm">
                                                    {{ strtoupper(substr($act->pegawai->user->name, 0, 2)) }}
                                                </div>
                                                
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900 truncate 
                                                    group-hover:text-indigo-700 transition-colors">
                                                        {{ $act->pegawai->user->name }}
                                                    </p>
    
                                                    <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                                                    {{ $act->uraian_tugas }}
                                                </p>
                                                </div>
                                                <div class="text-right flex flex-col items-end">
                                                    <span class="text-[10px] font-medium text-gray-400">
                                                        {{ $act->created_at->diffForHumans() }}
                                                    </span>
                                                    @php
                                                        $styles = [
                                                            'disetujui' => ['bg' => 'bg-green-50',  'text' => 'text-green-700',  'icon' => 'check-circle'],
                                                            'ditolak'   => ['bg' => 'bg-red-50',    'text' => 'text-red-700',    'icon' => 'x-circle'],
                                                            'revisi'    => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'icon' => 'alert-circle'],
                                                            'default'   => ['bg' => 'bg-gray-100',  'text' => 'text-gray-600',  'icon' => 'clock'],
                                                        ];
                                                        $st = $styles[$act->status] ?? $styles['default'];
                                                    @endphp

                                                    <span class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-md 
                                                        text-xs font-medium {{ $st['bg'] }} {{ $st['text'] }}">
                                                        <i data-feather="{{ $st['icon'] }}" class="w-3 h-3 mr-1"></i>
                                                        {{ ucfirst($act->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        @else
                            <p class="text-sm text-gray-500">Belum ada aktivitas terbaru.</p>
                        @endif

                    </div>

                </div>



                {{-- ============================
                     KOLOM KANAN (Quick Actions)
                ============================ --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden sticky top-6">

                        {{-- Header --}}
                        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-br from-blue-600 to-blue-700">
                            <h3 class="font-bold text-lg text-white flex items-center gap-2">
                                <i data-feather="zap" class="w-5 h-5 text-yellow-300"></i>
                                Menu Pintasan
                            </h3>
                            <p class="text-blue-100 text-xs mt-1">Aksi cepat untuk aktivitas harian.</p>
                        </div>


                        <div class="p-5 space-y-4">

                            {{-- Ajukan Izin --}}
                            <a href="{{ route('pegawai.izin.create') }}"
                            class="group flex items-center p-4 bg-white border border-gray-200 rounded-xl 
                                   shadow-sm hover:shadow-md hover:border-blue-500 hover:ring-1 hover:ring-blue-500 transition">

                                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition">
                                    <i data-feather="file-plus" class="w-6 h-6"></i>
                                </div>

                                <div class="ml-4">
                                    <h4 class="font-bold text-gray-800 group-hover:text-blue-700">Ajukan Izin / Sakit</h4>
                                    <p class="text-xs text-gray-500">Buat permohonan izin baru.</p>
                                </div>
                            </a>


                            {{-- Tambah Aktivitas --}}
                            <a href="{{ route('pegawai.aktivitas.create') }}"
                            class="group flex items-center p-4 bg-white border border-gray-200 rounded-xl 
                                   shadow-sm hover:shadow-md hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500 transition">

                                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition">
                                    <i data-feather="edit-3" class="w-6 h-6"></i>
                                </div>

                                <div class="ml-4">
                                    <h4 class="font-bold text-gray-800 group-hover:text-indigo-700">Laporan Aktivitas</h4>
                                    <p class="text-xs text-gray-500">Tambah aktivitas kerja harian.</p>
                                </div>
                            </a>


                            {{-- Riwayat Kehadiran --}}
                            <a href="{{ route('pegawai.kehadiran.index') }}"
                            class="group flex items-center p-4 bg-white border border-gray-200 rounded-xl 
                                   shadow-sm hover:shadow-md hover:border-green-500 hover:ring-1 hover:ring-green-500 transition">

                                <div class="p-3 bg-green-50 text-green-600 rounded-lg group-hover:bg-green-600 group-hover:text-white transition">
                                    <i data-feather="calendar" class="w-6 h-6"></i>
                                </div>

                                <div class="ml-4">
                                    <h4 class="font-bold text-gray-800 group-hover:text-green-700">Riwayat Kehadiran</h4>
                                    <p class="text-xs text-gray-500">Lihat daftar kehadiran Anda.</p>
                                </div>
                            </a>
                                    {{-- Tombol Unduh Laporan --}}
                      <a href="{{ route('pegawai.penilaian.download') }}"
                        class="group flex items-center p-4 bg-white border border-gray-200 rounded-xl shadow-sm 
                            hover:shadow-md hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500 transition-all duration-200">

                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white">
                                <i data-feather="download" class="w-6 h-6"></i>
                            </div>

                            <div class="ml-4">
                                <h4 class="font-bold text-gray-800 group-hover:text-indigo-700">Unduh Penilaian</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Download hasil penilaian Anda.</p>
                            </div>

                        </a>
                        </div>


                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                            <p class="text-xs text-center text-gray-400">Sistem Kinerja v1.0</p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

</x-app-layout>