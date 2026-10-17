<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kehadiran extends Model
{
    protected $table = 'kehadiran';
    protected $primaryKey = 'id_kehadiran';
    public $timestamps = false;

    protected $fillable = [
        'bulan', 'tahun', 'id_penempatan', 'id_kehadi', 'jumlah_hadir', 'jumlah_tugas', 'jumlah_izin', 'jumlah_sakit'
    ];

    public function kehadiranLembaga()
    {
        return $this->belongsTo(KehadiranLembaga::class, 'id_kehadi', 'id_kehadiran_lembaga');
    }

    public function penempatan()
    {
        return $this->belongsTo(Penempatan::class, 'id_penempatan', 'id_penempatan');
    }
}
