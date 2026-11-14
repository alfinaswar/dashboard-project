<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class InvoiceProyek extends Model
{
    use SoftDeletes;

    protected $table = 'invoice_proyek';

    protected $guarded = ['id'];

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class);
    }

    protected function tanggalTerbit(): Attribute
    {
        return Attribute::make(get: fn ($value) => $value ? Carbon::parse($value) : null);
    }

    protected function tanggalJatuhTempo(): Attribute
    {
        return Attribute::make(get: fn ($value) => $value ? Carbon::parse($value) : null);
    }
}
