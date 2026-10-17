<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PotonganPengajar extends Model
{
    protected $table = 'potongan_pengajar';
    protected $primaryKey = 'id_potongan_pengajar';
    public $timestamps = false;
    protected $guarded = [];

    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class, 'id_pengajar', 'id_pengajar');
    }

    public function potongan()
    {
        return $this->belongsTo(Potongan::class, 'jenis_potongan', 'id_potongan');
    }
}
