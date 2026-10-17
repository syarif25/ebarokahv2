<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KehadiranLembaga extends Model
{
    protected $table = 'kehadiran_lembaga';
    protected $primaryKey = 'id_kehadiran_lembaga';
    public $timestamps = false;

    protected $fillable = [
        'id_lembaga', 'kategori', 'bulan', 'tahun', 'status', 'jumlah_total', 'catatan_umum_pimpinan', 'file'
    ];

    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class, 'id_lembaga', 'id_lembaga');
    }

    public function detail()
    {
        return $this->hasMany(Kehadiran::class, 'id_kehadi', 'id_kehadiran_lembaga');
    }

    public function riwayat()
    {
        return $this->hasMany(LogRiwayatBarokah::class, 'id_kehadiran_lembaga', 'id_kehadiran_lembaga')->orderBy('waktu_eksekusi', 'desc');
    }
}
