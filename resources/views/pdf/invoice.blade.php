<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><title>Invoice {{ $invoice->nomor_invoice }}</title><style>body{font-family:DejaVu Sans,sans-serif;color:#0f172a;font-size:12px}header{border-bottom:2px solid #0f172a;padding-bottom:18px;margin-bottom:30px}.muted{color:#64748b}.row{display:flex;justify-content:space-between;margin:8px 0}.total{font-size:18px;font-weight:bold;border-top:1px solid #cbd5e1;padding-top:14px;margin-top:24px}</style></head>
<body>
<header><div class="row"><div><h1>INVOICE</h1><div>{{ $invoice->nomor_invoice }}</div></div><div style="text-align:right"><strong>{{ $profile?->nama_lengkap ?? 'Freelancer' }}</strong><div>{{ $profile?->email }}</div><div>{{ $profile?->nomor_whatsapp }}</div></div></div></header>
<div class="row"><div><strong>Tagih kepada</strong><div>{{ $invoice->proyek->klien->nama_klien }}</div><div>{{ $invoice->proyek->klien->nama_perusahaan }}</div><div>{{ $invoice->proyek->klien->alamat }}</div></div><div><div>Tanggal terbit: {{ $invoice->tanggal_terbit->format('d/m/Y') }}</div><div>Jatuh tempo: {{ $invoice->tanggal_jatuh_tempo?->format('d/m/Y') ?? '—' }}</div></div></div>
<h2>{{ $invoice->proyek->judul_proyek }}</h2><p class="muted">{{ $invoice->proyek->deskripsi }}</p>
<div class="row total"><span>Total tagihan</span><span>{{ $invoice->proyek->mata_uang }} {{ number_format($invoice->nominal_tagihan, 0, ',', '.') }}</span></div>
<h3>Informasi pembayaran</h3><div>{{ $profile?->nama_bank }} · {{ $profile?->nomor_rekening }} · {{ $profile?->atas_nama_rekening }}</div>
</body></html>
