<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <i data-feather="edit" class="w-5 h-5 text-indigo-600"></i>
            Edit SKP - {{ $data->periode }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('skp.update', $data->id) }}">
                @csrf
                @method('PUT')

                {{-- Identitas --}}
                <div class="bg-white p-6 rounded-xl shadow border mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Identitas SKP</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="font-medium text-gray-700">Periode</label>
                            <input name="periode" required value="{{ old('periode', $data->periode) }}"
                                   class="w-full mt-1 rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $data->tanggal_mulai?->format('Y-m-d')) }}"
                                   class="w-full mt-1 rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="font-medium text-gray-700">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $data->tanggal_selesai?->format('Y-m-d')) }}"
                                   class="w-full mt-1 rounded-lg border-gray-300">
                        </div>
                    </div>
                </div>

                {{-- Capaian & Pola --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-xl shadow border">
                        <h4 class="font-semibold text-gray-700 mb-2">Capaian Kinerja Organisasi</h4>
                        <textarea name="capaian_kinerja_organisasi" rows="4" class="w-full border-gray-300 rounded-lg">{{ old('capaian_kinerja_organisasi', $data->capaian_kinerja_organisasi) }}</textarea>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow border">
                        <h4 class="font-semibold text-gray-700 mb-2">Pola Distribusi</h4>
                        <textarea name="pola_distribusi" rows="4" class="w-full border-gray-300 rounded-lg">{{ old('pola_distribusi', $data->pola_distribusi) }}</textarea>
                    </div>
                </div>

                {{-- Hasil Kerja (edit inline) --}}
                <div class="bg-white p-6 rounded-xl shadow border mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">Hasil Kerja</h4>

                    <div class="space-y-4">
                        @foreach($data->hasilKerja as $hk)
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <input type="hidden" name="hasil_kerja[{{ $hk->id }}][id]" value="{{ $hk->id }}">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="text-sm text-gray-600">Jenis</label>
                                        <select name="hasil_kerja[{{ $hk->id }}][jenis]" class="w-full mt-1 rounded-lg border-gray-300">
                                            <option value="Utama" {{ $hk->jenis=='Utama' ? 'selected' : '' }}>Utama</option>
                                            <option value="Hasil Kerja" {{ $hk->jenis=='Hasil Kerja' ? 'selected' : '' }}>Hasil Kerja</option>
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="text-sm text-gray-600">RHK</label>
                                        <input name="hasil_kerja[{{ $hk->id }}][rhk]" value="{{ old("hasil_kerja.{$hk->id}.rhk", $hk->rhk) }}" class="w-full mt-1 rounded-lg border-gray-300">
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="text-sm text-gray-600">Indikator</label>
                                    <textarea name="hasil_kerja[{{ $hk->id }}][indikator_kinerja]" rows="3" class="w-full mt-1 rounded-lg border-gray-300">{{ old("hasil_kerja.{$hk->id}.indikator_kinerja", $hk->indikator_kinerja) }}</textarea>
                                </div>

                                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="text-sm text-gray-600">Aspek</label>
                                        <input name="hasil_kerja[{{ $hk->id }}][aspek]" value="{{ old("hasil_kerja.{$hk->id}.aspek", $hk->aspek) }}" class="w-full mt-1 rounded-lg border-gray-300">
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-600">Target</label>
                                        <input name="hasil_kerja[{{ $hk->id }}][target]" value="{{ old("hasil_kerja.{$hk->id}.target", $hk->target) }}" class="w-full mt-1 rounded-lg border-gray-300">
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-600">Realisasi</label>
                                        <input name="hasil_kerja[{{ $hk->id }}][realisasi]" value="{{ old("hasil_kerja.{$hk->id}.realisasi", $hk->realisasi) }}" class="w-full mt-1 rounded-lg border-gray-300">
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="text-sm text-gray-600">Umpan Balik</label>
                                    <textarea name="hasil_kerja[{{ $hk->id }}][umpan_balik]" rows="2" class="w-full mt-1 rounded-lg border-gray-300">{{ old("hasil_kerja.{$hk->id}.umpan_balik", $hk->umpan_balik) }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- tombol tambah baris baru (alpine) --}}
                    <div x-data="{ newRows: [] }" class="mt-4">
                        <template x-for="(r, i) in newRows" :key="i">
                            <div class="p-4 border rounded-lg bg-white mb-3">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="text-sm text-gray-600">Jenis</label>
                                        <select :name="`new_hasil_kerja[${i}][jenis]`" class="w-full mt-1 rounded-lg border-gray-300">
                                            <option value="Utama">Utama</option>
                                            <option value="Hasil Kerja">Hasil Kerja</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-sm text-gray-600">RHK</label>
                                        <input :name="`new_hasil_kerja[${i}][rhk]`" class="w-full mt-1 rounded-lg border-gray-300">
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="text-sm text-gray-600">Indikator</label>
                                    <textarea :name="`new_hasil_kerja[${i}][indikator_kinerja]`" rows="3" class="w-full mt-1 rounded-lg border-gray-300"></textarea>
                                </div>

                                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <input :name="`new_hasil_kerja[${i}][aspek]`" placeholder="Aspek" class="rounded-lg border-gray-300 p-2">
                                    <input :name="`new_hasil_kerja[${i}][target]`" placeholder="Target" class="rounded-lg border-gray-300 p-2">
                                    <input :name="`new_hasil_kerja[${i}][realisasi]`" placeholder="Realisasi" class="rounded-lg border-gray-300 p-2">
                                </div>

                                <div class="mt-3">
                                    <label class="text-sm text-gray-600">Umpan Balik</label>
                                    <textarea :name="`new_hasil_kerja[${i}][umpan_balik]`" rows="2" class="w-full mt-1 rounded-lg border-gray-300"></textarea>
                                </div>
                            </div>
                        </template>

                        <div class="flex gap-2 mt-3">
                            <button type="button" @click="newRows.push({})" class="px-4 py-2 bg-blue-600 text-white rounded-xl">
                                + Tambah Baris Baru
                            </button>

                            <button type="button" @click="newRows.pop()" class="px-4 py-2 bg-gray-100 rounded-xl">
                                Hapus Baris Baru
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Perilaku ASN --}}
                <div class="bg-white p-6 rounded-xl shadow border mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">Perilaku ASN</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($data->perilaku as $p)
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <input type="hidden" name="perilaku[{{ $p->id }}][id]" value="{{ $p->id }}">
                                <h5 class="font-semibold text-gray-800">{{ $p->aspek }}</h5>

                                <label class="text-sm text-gray-600 mt-2 block">Perilaku</label>
                                <textarea name="perilaku[{{ $p->id }}][perilaku]" rows="2" class="w-full mt-1 rounded-lg border-gray-300">{{ old("perilaku.{$p->id}.perilaku", $p->perilaku) }}</textarea>

                                <label class="text-sm text-gray-600 mt-2 block">Ekspektasi</label>
                                <textarea name="perilaku[{{ $p->id }}][ekspektasi]" rows="2" class="w-full mt-1 rounded-lg border-gray-300">{{ old("perilaku.{$p->id}.ekspektasi", $p->ekspektasi) }}</textarea>

                                <label class="text-sm text-gray-600 mt-2 block">Umpan Balik</label>
                                <textarea name="perilaku[{{ $p->id }}][umpan_balik]" rows="2" class="w-full mt-1 rounded-lg border-gray-300">{{ old("perilaku.{$p->id}.umpan_balik", $p->umpan_balik) }}</textarea>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tambah aspek baru --}}
                    <div x-data="{ newAspek: '' }" class="mt-4">
                        <div class="flex gap-2">
                            <input x-model="newAspek" placeholder="Aspek baru (contoh: Kreatif)" class="rounded-lg border-gray-300 px-3 py-2 flex-1">
                            <button type="button" @click="$dispatch('add-aspek', newAspek); newAspek='';" class="px-4 py-2 bg-blue-600 text-white rounded-xl">
                                Tambah Aspek
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('skp.show', $data->id) }}" class="px-4 py-2 bg-white border rounded-lg text-gray-700">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl">Simpan Perubahan</button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
