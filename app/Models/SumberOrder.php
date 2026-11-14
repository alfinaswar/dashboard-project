<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SumberOrder extends Model
{
    use SoftDeletes;

    protected $table = 'sumber_order';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['persentase_potongan_default' => 'decimal:2', 'apakah_platform' => 'boolean', 'aktif' => 'boolean'];
    }

    public function proyek(): HasMany
    {
        return $this->hasMany(Proyek::class);
    }
}
