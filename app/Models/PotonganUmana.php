<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PotonganUmana extends Model
{
    protected $table = 'potongan_umana';
    protected $primaryKey = 'id_potongan_umana';
    public $timestamps = false;
    protected $guarded = [];

    public function penempatan()
    {
        return $this->belongsTo(Penempatan::class, 'id_penempatan', 'id_penempatan');
    }

    public function potongan()
    {
        return $this->belongsTo(Potongan::class, 'jenis_potongan', 'id_potongan');
    }
}
