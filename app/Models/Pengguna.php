<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Pengguna extends Authenticatable
{
    use HasRoles;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false;
    protected $guarded = [];

    public function lembagaData()
    {
        return $this->belongsTo(Lembaga::class, 'lembaga', 'id_lembaga');
    }
}
