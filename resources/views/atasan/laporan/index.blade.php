<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Laporan Kehadiran</h2>
    </x-slot>

    @php
        $totalKaryawan = $rekap->count();
        $totalHadir    = $rekap->sum('hadir');
        $totalIzin     = $rekap->sum('izin');
        $totalSakit    = $rekap->sum('sakit');
        $totalCuti     = $rekap->sum('cuti');
        $totalAlpha    = $rekap->sum('alpha');
    @endphp

    <div class="py-6 mx-auto  sm:px-6 lg:px-8 space-y-6">

        {{-- HERO BANNER & TOMBOL EXPORT UTAMA --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 p-6 text-white shadow-lg">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-xs text-xs font-semibold tracking-wide mb-2">
                        <i data-feather="calendar" class="w-3.5 h-3.5"></i>
                        <span>Periode: {{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}</span>
                    </div>
                    <h3 class="text-2xl font-extrabold tracking-tight">
                        Laporan Rekapitulasi Kehadiran Karyawan
                    </h3>
                    <p class="text-sm text-indigo-100 mt-1">
                        PT Pou Yuen Indonesia — Rekap otomatis hasil deteksi kamera AI (YOLOv8 &amp; CNN) serta pengajuan izin/sakit/cuti.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('atasan.laporan.export.pdf', ['cari' => request('cari'), 'tanggal_dari' => $dari, 'tanggal_sampai' => $sampai]) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-rose-600 hover:bg-rose-50 font-bold text-sm rounded-xl shadow-md transition">
                        <i data-feather="printer" class="w-4 h-4"></i>
                        <span>Cetak PDF Rekap</span>
                    </a>
                    <a href="{{ route('atasan.laporan.export.excel', ['cari' => request('cari'), 'tanggal_dari' => $dari, 'tanggal_sampai' => $sampai]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm rounded-xl shadow-md border border-emerald-400/40 transition">
                        <i data-feather="download" class="w-4 h-4"></i>
                        <span>Export Excel (CSV)</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- KARTU RINGKASAN STATISTIK (KPI CARDS) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i data-feather="users" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Karyawan</p>
                    <p class="text-xl font-extrabold text-slate-800">{{ $totalKaryawan }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-emerald-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-feather="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Hadir</p>
                    <p class="text-xl font-extrabold text-emerald-600">{{ $totalHadir }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-blue-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-feather="file-text" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Izin</p>
                    <p class="text-xl font-extrabold text-blue-600">{{ $totalIzin }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-amber-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-feather="thermometer" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Sakit / Cuti</p>
                    <p class="text-xl font-extrabold text-amber-600">{{ $totalSakit }} <span class="text-slate-400 font-normal text-sm">/</span> {{ $totalCuti }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-rose-200/80 shadow-xs flex items-center gap-3.5 col-span-2 sm:col-span-1">
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <i data-feather="x-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Alpha</p>
                    <p class="text-xl font-extrabold text-rose-600">{{ $totalAlpha }}</p>
                </div>
            </div>
        </div>

        {{-- PANEL FILTER & TABEL REKAP --}}
        <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-200/90">

            {{-- BAR FILTER PENCARIAN & TANGGAL --}}
            <div class="p-5 border-b border-slate-100 bg-slate-50/60">
                <form method="GET" action="{{ route('atasan.laporan.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Nama Karyawan</label>
                        <div class="relative">
                            <input type="text" name="cari" value="{{ request('cari') }}"
                                   placeholder="Ketik nama karyawan..."
                                   class="w-full pl-9 pr-3 py-2 rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <i data-feather="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" value="{{ $dari }}"
                               class="w-full py-2 px-3 rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" value="{{ $sampai }}"
                               class="w-full py-2 px-3 rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-2 flex items-center gap-2">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm shadow-xs transition">
                            <i data-feather="filter" class="w-4 h-4"></i>
                            <span>Filter</span>
                        </button>
                        @if(request()->filled('cari') || request()->filled('tanggal_dari') || request()->filled('tanggal_sampai'))
                            <a href="{{ route('atasan.laporan.index') }}"
                               title="Reset Filter"
                               class="p-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-sm transition">
                                <i data-feather="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- TABEL REKAPITULASI --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5">NIK Karyawan</th>
                            <th class="px-4 py-3.5">Nama Karyawan</th>
                            <th class="px-4 py-3.5">Departemen / Divisi</th>
                            <th class="px-4 py-3.5">Jabatan</th>
                            <th class="px-3 py-3.5 text-center">Hadir</th>
                            <th class="px-3 py-3.5 text-center">Izin</th>
                            <th class="px-3 py-3.5 text-center">Sakit</th>
                            <th class="px-3 py-3.5 text-center">Cuti</th>
                            <th class="px-3 py-3.5 text-center">Alpha</th>
                            <th class="px-4 py-3.5 text-center">Cetak Individu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($rekap as $idx => $r)
                            <tr class="hover:bg-indigo-50/40 transition-colors">
                                <td class="px-4 py-3.5 text-center font-semibold text-slate-400">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-mono text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $r->nik_karyawan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white font-bold text-xs flex items-center justify-center shadow-2xs shrink-0">
                                            {{ strtoupper(substr($r->nama, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 leading-tight">{{ $r->nama }}</p>
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $r->jenis_kelamin }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-semibold text-slate-700 text-xs">{{ $r->departemen }}</p>
                                    @if($r->divisi && $r->divisi !== '-')
                                        <p class="text-xs text-slate-500 mt-0.5">Divisi: {{ $r->divisi }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $r->jabatan }}
                                    </span>
                                </td>
                                <td class="px-3 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold {{ $r->hadir > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $r->hadir }}
                                    </span>
                                </td>
                                <td class="px-3 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold {{ $r->izin > 0 ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $r->izin }}
                                    </span>
                                </td>
                                <td class="px-3 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold {{ $r->sakit > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $r->sakit }}
                                    </span>
                                </td>
                                <td class="px-3 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold {{ $r->cuti > 0 ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $r->cuti }}
                                    </span>
                                </td>
                                <td class="px-3 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold {{ $r->alpha > 0 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $r->alpha }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <a target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 border border-rose-200 rounded-xl text-xs font-semibold hover:bg-rose-600 hover:text-white hover:border-rose-600 transition shadow-2xs"
                                       href="{{ route('laporan.kehadiran.perpegawai', ['pegawai_id' => $r->id, 'tanggal_dari' => $dari, 'tanggal_sampai' => $sampai]) }}">
                                       <span>Cetak</span>
                                        <i data-feather="file-text" class="w-3.5 h-3.5"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <i data-feather="inbox" class="w-10 h-10 mb-2 stroke-1"></i>
                                        <p class="text-sm font-medium text-slate-600">Tidak ada data karyawan yang ditemukan.</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Coba ubah kata kunci pencarian atau rentang tanggal filter.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
