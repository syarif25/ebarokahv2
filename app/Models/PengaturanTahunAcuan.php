<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanTahunAcuan extends Model
{
    protected $table = 'pengaturan_tahun_acuan';
    public $timestamps = false;
    protected $fillable = ['id_bidang', 'tahun_acuan', 'keterangan'];
}
