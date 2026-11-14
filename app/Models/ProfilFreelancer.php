<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfilFreelancer extends Model
{
    use SoftDeletes;

    protected $table = 'profil_freelancer';

    protected $guarded = ['id'];

    protected $hidden = ['nomor_ktp', 'nomor_rekening', 'tanda_tangan_path'];

    protected function casts(): array
    {
        return ['nomor_ktp' => 'encrypted', 'nomor_rekening' => 'encrypted'];
    }
}
