<h2>Laporan Penilaian Pegawai</h2>

<p>Nama Pegawai: {{ $pegawai->user->name }}</p>
<p>Atasan: {{ $atasan->name ?? '-' }}</p>
<p>Nilai Total: {{ $penilaian->nilai_total }}</p>
<p>Kategori: {{ $penilaian->kategori }}</p>
