<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Rekap Kehadiran
        </h2>
    </x-slot>

    <div x-data="efektivitasModal()" @keydown.escape.window="close()">
        <div class=" mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="{{ route('pegawai.kehadiran.index') }}"
                           class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                            <i data-feather="clock" class="w-5 h-5 mr-2"></i>
                            Rekap Kehadiran
                        </a>

                        <a href="{{ route('pegawai.izin.index') }}"
                           class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                            <i data-feather="file-text" class="w-5 h-5 mr-2"></i>
                            Izin / Sakit / Cuti
                        </a>
                        
                    </nav>
                </div>
            </div>



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

            {{-- KARTU AKTIVASI KODE REFERAL HARIAN & KUNCI CIRI VISUAL ZONA --}}
            @if(isset($aktivasiHariIni) && $aktivasiHariIni)
                <div class="mb-6 rounded-2xl border {{ $aktivasiHariIni->is_active ? 'bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 border-emerald-200' : 'bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 border-amber-200' }} p-6 shadow-sm">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div class="space-y-2 flex-1">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $aktivasiHariIni->is_active ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white animate-pulse' }}">
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                    {{ $aktivasiHariIni->is_active ? 'ZONA AKTIF (SIAP DETEKSI)' : 'ZONA DISABLE (BELUM AKTIF)' }}
                                </span>
                                <span class="text-xs font-semibold text-gray-500">
                                    Reset Otomatis Setiap Hari &bull; {{ now()->translatedFormat('l, d F Y') }}
                                </span>
                                @if($aktivasiHariIni->ciri_visual)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                        🔒 Ciri Visual Hari Ini Terkunci ({{ $aktivasiHariIni->first_entered_at ? $aktivasiHariIni->first_entered_at->format('H:i') : 'Aktif' }})
                                    </span>
                                @elseif($aktivasiHariIni->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                        ⏳ Menunggu Masuk Zona Pertama Kali untuk Simpan Ciri Visual
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg font-extrabold text-gray-900">
                                {{ $aktivasiHariIni->is_active ? 'Konfirmasi Kehadiran & Zona Kamera Telah Aktif' : 'Wajib Input Kode Referal Harian Sebelum Absen Kamera' }}
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                @if($aktivasiHariIni->is_active)
                                    Zona kerja Anda sudah aktif hari ini (dikonfirmasi pukul <strong>{{ $aktivasiHariIni->activated_at ? $aktivasiHariIni->activated_at->format('H:i:s') : '-' }}</strong>). Saat Anda pertama kali masuk ke zona, sistem otomatis menyimpan ciri-ciri visual Anda hari ini agar zona tidak mendeteksi orang lain.
                                @else
                                    Masukkan <strong>Kode Referal Harian</strong> di samping untuk mengonfirmasi kehadiran dan mengaktifkan zona kamera Anda. <strong>Jika tidak diinput, zona Anda akan tetap nonaktif (disable) dan kamera tidak akan mencatat kehadiran Anda hari ini.</strong>
                                @endif
                            </p>
                        </div>

                        {{-- Panel Kode Referal & Form Input --}}
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 shrink-0">
                            <div class="bg-white px-5 py-3.5 rounded-xl border border-gray-200 shadow-2xs text-center">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Kode Referal Hari Ini</p>
                                <p class="text-2xl font-mono font-black tracking-widest text-indigo-700 mt-0.5 select-all">{{ $aktivasiHariIni->kode_referal }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Ketik kode ini untuk aktivasi</p>
                            </div>

                            @if(!$aktivasiHariIni->is_active)
                                <form method="POST" action="{{ route('pegawai.kehadiran.aktivasi_referal') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                                    @csrf
                                    <div>
                                        <input type="text"
                                               name="kode_referal"
                                               placeholder="Masukkan Kode.."
                                               required
                                               autocomplete="off"
                                               class="w-full sm:w-56 px-4 py-3 rounded-xl border-gray-300 font-mono font-bold text-sm uppercase tracking-wider focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs">
                                    </div>
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition cursor-pointer">
                                        <i data-feather="unlock" class="w-4 h-4"></i>
                                        Aktifkan Zona
                                    </button>
                                </form>
                            @else
                                <div class="flex items-center gap-3 bg-emerald-600/10 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl">
                                    @if($aktivasiHariIni->snapshot_wajah)
                                        <img src="{{ $aktivasiHariIni->snapshot_wajah }}" alt="Snapshot Ciri" class="w-12 h-12 rounded-lg object-cover border border-emerald-400 shadow-2xs" title="Ciri visual pertama masuk zona hari ini">
                                    @endif
                                    <div>
                                        <p class="text-xs font-extrabold uppercase tracking-wider text-emerald-700">✓ Zona Terkonfirmasi</p>
                                        <p class="text-xs text-emerald-600">
                                            {{ $aktivasiHariIni->ciri_visual ? 'Identitas visual terkunci hari ini' : 'Silakan masuk ke zona kamera' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif


            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-5 border-b bg-gray-50 flex flex-wrap items-center justify-between gap-4 rounded-lg">
            <div class="flex items-center gap-3">
                <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                    <i data-feather="clock" class="w-6 h-6"></i>
                </span>

                <div>
                  <h3 class="text-lg font-bold text-gray-800">Riwayat Kehadiran Karyawan</h3>
                    <p class="text-sm text-gray-500">Hasil monitoring kehadiran otomatis (harian &amp; bulanan).</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('pegawai.kehadiran.index') }}" class="flex flex-wrap items-center gap-2">
                    <select name="mode" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm">
                        <option value="bulanan" @selected(($mode ?? 'bulanan') === 'bulanan')>Per Bulan</option>
                        <option value="harian" @selected(($mode ?? 'bulanan') === 'harian')>Harian</option>
                    </select>
                    @if(($mode ?? 'bulanan') === 'harian')
                        <input type="date" name="tanggal" value="{{ $tanggal ?? date('Y-m-d') }}" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm">
                    @else
                        <input type="month" name="bulan" value="{{ $bulan ?? date('Y-m') }}" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm">
                    @endif
                </form>

                <a href="{{ route('laporan.kehadiran.perpegawai', ['pegawai_id' => $pegawai->id ?? null]) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow transition">
                    <i data-feather="file-text" class="w-4 h-4"></i>
                    Cetak Laporan PDF
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
                                    <th scope="col" class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Absen Masuk
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Absen Pulang
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Detail
                                    </th>
                                </tr>
                            </thead>
                            
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($data as $row)
                                    <tr class="hover:bg-gray-50 transition-colors">

                                        {{-- Tanggal --}}
                                        <td class="px-6 py-4 whitespace-nowrap ">
                                            <div class="text-base text-gray-900">
                                                {{ \Carbon\Carbon::parse($row->tanggal)->isoFormat('D MMMM YYYY') }}
                                            </div>
                                        </td>

                                        {{-- Absen Masuk --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($row->jenis != 'hadir')
                                                <span class="text-base text-blue-600 font-semibold text-center">
                                                    {{ ucfirst($row->jenis) }}
                                                </span>
                                            @else
                                                <div class="text-base text-gray-900 text-center">
                                                    {{ $row->check_in ? \Carbon\Carbon::parse($row->check_in)->format('H:i') : '-' }}
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Absen Pulang --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                             @if ($row->jenis != 'hadir')
                                                <span class="text-base text-blue-600 font-semibold text-center">
                                                    {{ ucfirst($row->jenis) }}
                                                </span>
                                            @else
                                                @if ($row->check_out)
                                                    <div class="text-base text-gray-900">
                                                        {{ \Carbon\Carbon::parse($row->check_out)->format('H:i') }}
                                                    </div>
                                                @else
                                                    <span class="text-base text-center text-gray-400 italic">Belum Absen Pulang</span>
                                                @endif
                                            @endif
                                        </td>

                                        {{-- Detail Efektivitas --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($row->jenis == 'hadir')
                                                <button type="button"
                                                    @click="openDetail('{{ $pegawai->id }}', '{{ $row->tanggal }}')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-lg border border-indigo-200 transition-all duration-150">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Info Kehadiran
                                                </button>
                                            @else
                                                <span class="text-base text-gray-400 italic">-</span>
                                            @endif
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-base text-gray-500">
                                            Belum ada data kehadiran.
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

    {{-- ════════════════════════════════════════════════════ --}}
    {{-- MODAL EFEKTIVITAS KERJA (AI-Powered)                --}}
    {{-- ════════════════════════════════════════════════════ --}}

    {{-- Backdrop --}}
    <template x-teleport="body">
        <div style="position: relative; z-index: 99999;">
            <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/50 backdrop-blur-sm" style="display:none"
                 @click="close()"></div>

            {{-- Modal Panel --}}
            <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="fixed inset-0 flex items-center justify-center p-4" style="display:none">

                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden relative">

                    {{-- Header --}}
                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-bold text-lg">Info Progres Kehadiran</h3>
                                    <p class="text-indigo-200 text-sm" x-text="data ? data.tanggal_label : ''"></p>
                                </div>
                            </div>
                            <button @click="close()" class="p-2 rounded-xl text-white/70 hover:text-white hover:bg-white/20 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5">

                        {{-- Loading --}}
                        <div x-show="loading" class="py-12 flex flex-col items-center gap-3">
                            <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                            <p class="text-sm text-gray-500">Memuat data dari AI...</p>
                        </div>

                        {{-- Error --}}
                        <div x-show="!loading && error" class="py-8 text-center">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-red-500 text-sm font-medium" x-text="error"></p>
                        </div>

                        {{-- Data --}}
                        <div x-show="!loading && data && !error" class="space-y-5">

                            {{-- Efektivitas Gauge --}}
                            <div class="bg-gradient-to-br from-slate-50 to-indigo-50 rounded-2xl p-5 border border-indigo-100">
                                <div class="flex items-end justify-between mb-2">
                                    <div>
                                        <span class="text-sm font-semibold text-gray-700">Progres Pemenuhan Jam Kerja</span>
                                        <p class="text-xs text-gray-500 mt-0.5"
                                           x-text="(data?.efektivitas_persen || 0) >= 100 ? '✓ Target 8 Jam Terpenuhi (Dianggap Hadir Penuh)' : 'Target: 8 Jam untuk dianggap hadir penuh'"></p>
                                    </div>
                                    <span class="text-2xl font-bold"
                                        :class="(data?.efektivitas_persen||0) >= 100 ? 'text-emerald-600' : (data?.efektivitas_persen||0) >= 50 ? 'text-amber-500' : 'text-red-500'"
                                        x-text="(data?.efektivitas_persen || 0) + '%'">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                    <div class="h-3 rounded-full transition-all duration-700"
                                        :class="(data?.efektivitas_persen||0) >= 100 ? 'bg-emerald-500' : (data?.efektivitas_persen||0) >= 50 ? 'bg-amber-400' : 'bg-red-400'"
                                        :style="'width:' + (data?.efektivitas_persen || 0) + '%'"></div>
                                </div>
                                <div class="flex justify-between mt-1.5 text-xs text-gray-400">
                                    <span>0 Jam (0%)</span><span>Standar: 8 Jam</span><span>8 Jam (100%)</span>
                                </div>
                            </div>

                            {{-- Stats Grid --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                    <p class="text-xs text-blue-500 font-medium mb-1">⏰ Absen Masuk</p>
                                    <p class="text-lg font-bold text-blue-800" x-text="data?.check_in || '-'">-</p>
                                </div>
                                <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                                    <p class="text-xs text-purple-500 font-medium mb-1">🏁 Absen Pulang</p>
                                    <p class="text-lg font-bold text-purple-800" x-text="data?.check_out || 'Belum'">-</p>
                                </div>
                                <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                                    <p class="text-xs text-emerald-600 font-medium mb-1">📍 Durasi Di Meja</p>
                                    <p class="text-base font-bold text-emerald-800" x-text="data?.durasi_di_meja || '-'">-</p>
                                    <p class="text-xs text-emerald-500 mt-0.5">Terdeteksi AI CCTV</p>
                                </div>
                                <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
                                    <p class="text-xs text-orange-500 font-medium mb-1">🕐 Total Hadir</p>
                                    <p class="text-base font-bold text-orange-800" x-text="data?.durasi_hadir || '-'">-</p>
                                    <p class="text-xs text-orange-400 mt-0.5">Masuk → Pulang</p>
                                </div>
                            </div>

                            {{-- AI Status Badge --}}
                            {{-- isActive = ada log DAN status bukan 'belum_terdeteksi' (monitoring aktif) --}}
                            <div class="flex items-center gap-3 p-3 rounded-xl border"
                                :class="(data?.ai_detected && data?.status_raw !== 'belum_terdeteksi') ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50 border-gray-200'">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                    :class="(data?.ai_detected && data?.status_raw !== 'belum_terdeteksi') ? 'bg-emerald-500' : 'bg-gray-300'">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold"
                                        :class="(data?.ai_detected && data?.status_raw !== 'belum_terdeteksi') ? 'text-emerald-700' : 'text-gray-500'"
                                        x-text="(data?.ai_detected && data?.status_raw !== 'belum_terdeteksi')
                                            ? 'Data terdeteksi oleh AI CCTV'
                                            : (data?.ai_detected ? 'Pengawasan sedang tidak aktif' : 'Belum ada data dari AI CCTV')"></p>
                                    <p class="text-xs mt-0.5"
                                        :class="(data?.ai_detected && data?.status_raw !== 'belum_terdeteksi') ? 'text-emerald-500' : 'text-gray-400'"
                                        x-text="(data?.ai_detected && data?.status_raw !== 'belum_terdeteksi')
                                            ? 'Status: ' + data.status_terakhir
                                            : (data?.ai_detected ? 'Kamera pengawas tidak aktif saat ini' : 'Pastikan server Python aktif')"></p>
                                </div>
                            </div>

                        </div>{{-- /data --}}
                    </div>{{-- /body --}}

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <button type="button" @click="close()" class="px-5 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">Tutup</button>
                    </div>

                </div>
            </div>
        </div>
    </template>

</div>{{-- /x-data wrapper --}}

</x-app-layout>

<script>
function efektivitasModal() {
    return {
        isOpen: false, loading: false,
        data: null, error: null,

        openDetail(pegawaiId, tanggal) {
            this.isOpen = true;
            this.loading = true;
            this.data = null;
            this.error = null;
            fetch(`http://localhost:5000/api/efektivitas/${pegawaiId}/${tanggal}`)
                .then(r => r.json())
                .then(d => {
                    if (d.error) { this.error = d.error; }
                    else { this.data = d; }
                })
                .catch(() => { this.error = 'Gagal terhubung ke server AI. Pastikan server Python aktif.'; })
                .finally(() => { this.loading = false; });
        },

        close() { this.isOpen = false; this.data = null; this.error = null; }
    };
}
</script>