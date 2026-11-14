<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>SPK {{ $proyek->nomor_kontrak }}</title><style>body{font-family:DejaVu Sans,sans-serif;color:#111827;font-size:12px;line-height:1.7}h1{text-align:center;font-size:18px}h2{font-size:14px;margin-top:24px}.sign{display:flex;justify-content:space-between;margin-top:80px;text-align:center}.line{margin-top:70px;border-top:1px solid #111;padding-top:6px}</style></head><body>
<h1>SURAT PERJANJIAN KERJA SAMA</h1><p style="text-align:center">Nomor: {{ $proyek->nomor_kontrak }}</p>
<p>Pada hari ini, {{ now()->translatedFormat('d F Y') }}, pihak berikut sepakat untuk bekerja sama.</p>
<p><strong>Pihak Pertama:</strong> {{ $profile->nama_lengkap }}, {{ $profile->alamat }}.</p><p><strong>Pihak Kedua:</strong> {{ $proyek->klien->nama_klien }}@if($proyek->klien->nama_perusahaan), {{ $proyek->klien->nama_perusahaan }}@endif, {{ $proyek->klien->alamat }}.</p>
<h2>Pasal 1 — Lingkup pekerjaan</h2><p>{{ $proyek->deskripsi ?: $proyek->judul_proyek }}</p><p>Teknologi: {{ $proyek->tech_stack ?: 'Sesuai kebutuhan proyek' }}.</p>
<h2>Pasal 2 — Nilai pekerjaan</h2><p>Nilai bruto {{ $proyek->mata_uang }} {{ number_format($proyek->nilai_kotor, 0, ',', '.') }}. Potongan platform {{ $proyek->persentase_potongan_platform }}%. Nilai bersih {{ $proyek->mata_uang }} {{ number_format($proyek->nilai_bersih_kontrak, 0, ',', '.') }}.</p>
<h2>Pasal 3 — Waktu pengerjaan</h2><p>Mulai {{ $proyek->tanggal_mulai->format('d/m/Y') }}. Target selesai {{ $proyek->tanggal_deadline?->format('d/m/Y') ?: 'disepakati bersama' }}.</p>
<p>Surat perjanjian ini dibuat dan disetujui kedua pihak.</p><div class="sign"><div>Pihak Pertama<div class="line">{{ $profile->nama_lengkap }}</div></div><div>Pihak Kedua<div class="line">{{ $proyek->klien->nama_klien }}</div></div></div>
</body></html>
