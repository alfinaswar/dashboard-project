<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Klien extends Model
{
    use SoftDeletes;

    protected $table = 'klien';

    protected $guarded = ['id'];

    public function proyek(): HasMany
    {
        return $this->hasMany(Proyek::class);
    }
}
