<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogRiwayatBarokah extends Model
{
    protected $table = 'log_riwayat_barokah';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'id_kehadiran_lembaga', 'status_aksi', 'waktu_eksekusi', 'id_pengguna', 'catatan_log'
    ];

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id'); // Assuming the User model pk is 'id' or 'id_pengguna'
    }

    public function kehadiranLembaga()
    {
        return $this->belongsTo(KehadiranLembaga::class, 'id_kehadiran_lembaga', 'id_kehadiran_lembaga');
    }
}
