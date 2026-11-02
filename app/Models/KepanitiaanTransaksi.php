<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KepanitiaanTransaksi extends Model
{
    protected $table = 'kepanitiaan_transaksi';
    protected $primaryKey = 'id_kepanitiaan_transaksi';
    protected $guarded = [];

    public function jenisKegiatan()
    {
        return $this->belongsTo(MasterJenisKegiatan::class, 'id_jenis_kegiatan', 'id_jenis_kegiatan');
    }

    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class, 'id_lembaga', 'id_lembaga');
    }

    public function details()
    {
        return $this->hasMany(KepanitiaanDetail::class, 'id_kepanitiaan_transaksi', 'id_kepanitiaan_transaksi');
    }
}
