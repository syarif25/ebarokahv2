<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarokahKepanitiaan extends Model
{
    protected $table = 'barokah_kepanitiaan';
    protected $primaryKey = 'id_barokah_kepanitiaan';
    protected $guarded = [];

    public function jenisKegiatan()
    {
        return $this->belongsTo(MasterJenisKegiatan::class, 'id_jenis_kegiatan', 'id_jenis_kegiatan');
    }
}
