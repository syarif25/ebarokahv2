<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterJenisKegiatan extends Model
{
    protected $table = 'master_jenis_kegiatan';
    protected $primaryKey = 'id_jenis_kegiatan';
    protected $guarded = [];

    public function tarif()
    {
        return $this->hasMany(BarokahKepanitiaan::class, 'id_jenis_kegiatan', 'id_jenis_kegiatan');
    }

    public function transaksi()
    {
        return $this->hasMany(KepanitiaanTransaksi::class, 'id_jenis_kegiatan', 'id_jenis_kegiatan');
    }
}
