<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>Laporan Kehadiran</title>
<style>
body{font-family:Arial,sans-serif;font-size:10pt}
h1{font-size:12pt;text-align:center;margin:0}
p.s{text-align:center;margin:4px 0 14px}
table{width:100%;border-collapse:collapse}
th,td{border:1px solid #000;padding:5px;font-size:9pt}
th{background:#f0f0f0}
.c{text-align:center}
</style></head><body>
<h1>LAPORAN KEHADIRAN KARYAWAN PT POU YUEN</h1>
<p class="s">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
<table>
<tr><th>No</th><th>Nama</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Cuti</th><th>Alpha</th></tr>
@foreach ($rekap as $i => $r)
<tr><td class="c">{{ $i + 1 }}</td><td>{{ $r->nama }}</td>
<td class="c">{{ $r->hadir }}</td><td class="c">{{ $r->izin }}</td><td class="c">{{ $r->sakit }}</td><td class="c">{{ $r->cuti }}</td><td class="c">{{ $r->alpha }}</td></tr>
@endforeach
</table></body></html>
