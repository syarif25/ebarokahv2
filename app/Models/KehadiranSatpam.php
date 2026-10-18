<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KehadiranSatpam extends Model
{
    protected $table = 'kehadiran_satpam';
    protected $primaryKey = 'id_kehadiran_satpam';
    public $timestamps = false;
    protected $guarded = [];

    public function satpam()
    {
        return $this->belongsTo(Satpam::class, 'id_satpam', 'id_satpam');
    }

    public function kehadiranLembaga()
    {
        return $this->belongsTo(KehadiranLembaga::class, 'id_kehadiran_lembaga', 'id_kehadiran_lembaga');
    }
}
