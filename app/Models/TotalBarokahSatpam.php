<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TotalBarokahSatpam extends Model
{
    protected $table = 'total_barokah_satpam';
    protected $primaryKey = 'id_total_barokah_satpam';
    public $timestamps = false;
    protected $guarded = [];

    public function satpam()
    {
        return $this->belongsTo(Satpam::class, 'id_satpam', 'id_satpam');
    }

    public function kehadiranSatpam()
    {
        return $this->belongsTo(KehadiranSatpam::class, 'id_kehadiran_satpam', 'id_kehadiran_satpam');
    }

    public function kehadiranLembaga()
    {
        return $this->belongsTo(KehadiranLembaga::class, 'id_kehadiran_lembaga', 'id_kehadiran_lembaga');
    }
}
