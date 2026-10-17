<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PotonganSatpam extends Model
{
    protected $table = 'potongan_satpam';
    protected $primaryKey = 'id_potongan_satpam';
    public $timestamps = false;
    protected $guarded = [];

    public function satpam()
    {
        return $this->belongsTo(Satpam::class, 'id_satpam', 'id_satpam');
    }

    public function potongan()
    {
        return $this->belongsTo(Potongan::class, 'jenis_potongan', 'id_potongan');
    }
}
