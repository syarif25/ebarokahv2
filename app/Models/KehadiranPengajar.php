<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KehadiranPengajar extends Model
{
    use HasFactory;

    protected $table = 'kehadiran_pengajar';
    protected $primaryKey = 'id_kehadiran_pengajar';
    public $timestamps = false;

    protected $fillable = [
        'bulan',
        'tahun',
        'id_pengajar',
        'id_kehadiran_lembaga',
        'jumlah_hadir',
        'jumlah_hadir_15',
        'jumlah_hadir_10',
        'jumlah_hadir_piket',
        'tgl_input',
    ];

    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class, 'id_pengajar', 'id_pengajar');
    }

    public function kehadiranLembaga()
    {
        return $this->belongsTo(KehadiranLembaga::class, 'id_kehadiran_lembaga', 'id_kehadiran_lembaga');
    }
}
