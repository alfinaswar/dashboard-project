<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proyek extends Model
{
    use SoftDeletes;

    protected $table = 'proyek';

    protected $guarded = ['id'];

    protected $appends = ['total_modal', 'laba_bersih', 'margin_keuntungan', 'total_dibayar', 'sisa_tagihan', 'apakah_terkunci'];

    protected function casts(): array
    {
        return [
            'nilai_kotor' => 'decimal:2',
            'persentase_potongan_platform' => 'decimal:2',
            'nominal_potongan_platform' => 'decimal:2',
            'nilai_bersih_kontrak' => 'decimal:2',
            'tanggal_mulai' => 'date',
            'tanggal_deadline' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function klien(): BelongsTo
    {
        return $this->belongsTo(Klien::class);
    }

    public function sumberOrder(): BelongsTo
    {
        return $this->belongsTo(SumberOrder::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(MasterStatusProyek::class, 'status_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(InvoiceProyek::class);
    }

    public function modals(): HasMany
    {
        return $this->hasMany(ModalProyek::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranProyek::class);
    }

    public function getTotalModalAttribute(): string
    {
        $total = $this->getAttribute('total_modal_raw');
        $total ??= $this->modals()->sum('jumlah_biaya');

        return number_format((float) $total, 2, '.', '');
    }

    public function getLabaBersihAttribute(): string
    {
        return number_format((float) $this->nilai_bersih_kontrak - (float) $this->total_modal, 2, '.', '');
    }

    public function getMarginKeuntunganAttribute(): string
    {
        return (float) $this->nilai_kotor === 0.0
            ? '0.00'
            : number_format(((float) $this->laba_bersih / (float) $this->nilai_kotor) * 100, 2, '.', '');
    }

    public function getTotalDibayarAttribute(): string
    {
        $total = $this->getAttribute('total_dibayar_raw');
        $total ??= $this->pembayaran()->sum('jumlah_bayar');

        return number_format((float) $total, 2, '.', '');
    }

    public function getSisaTagihanAttribute(): string
    {
        return number_format(max(0, (float) $this->nilai_bersih_kontrak - (float) $this->total_dibayar), 2, '.', '');
    }

    public function getApakahTerkunciAttribute(): bool
    {
        return $this->relationLoaded('status')
            ? $this->status->nama_status === 'Selesai / Lunas'
            : $this->status()->where('nama_status', 'Selesai / Lunas')->exists();
    }
}
