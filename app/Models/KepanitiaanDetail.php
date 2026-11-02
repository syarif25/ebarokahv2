<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KepanitiaanDetail extends Model
{
    protected $table = 'kepanitiaan_detail';
    protected $primaryKey = 'id_kepanitiaan_detail';
    protected $guarded = [];

    public function transaksi()
    {
        return $this->belongsTo(KepanitiaanTransaksi::class, 'id_kepanitiaan_transaksi', 'id_kepanitiaan_transaksi');
    }

    public function umana()
    {
        return $this->belongsTo(Umana::class, 'nik', 'nik');
    }
}
