<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajar extends Model
{
    protected $table = 'pengajar';
    protected $primaryKey = 'id_pengajar';
    public $timestamps = false;
    protected $guarded = [];

    // Relasi ke umana melalui NIK
    public function umana()
    {
        return $this->belongsTo(Umana::class, 'nik', 'nik');
    }

    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class, 'id_lembaga', 'id_lembaga');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id_prodi');
    }

    public function potongans()
    {
        return $this->hasMany(PotonganPengajar::class, 'id_pengajar', 'id_pengajar');
    }

    public function barokahTambahans()
    {
        return $this->hasMany(BarokahTambahan::class, 'id_pengajar', 'id_pengajar');
    }
}
