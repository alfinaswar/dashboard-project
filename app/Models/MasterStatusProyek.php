<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterStatusProyek extends Model
{
    use SoftDeletes;

    protected $table = 'master_status_proyek';

    protected $guarded = ['id'];

    public function proyek(): HasMany
    {
        return $this->hasMany(Proyek::class, 'status_id');
    }
}
