<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarokahKehormatanPengajar extends Model
{
    protected $table = 'barokah_kehormatan_pengajar';
    protected $primaryKey = 'id_barokah_kehormatan_pengajar';
    public $timestamps = false;
    protected $guarded = [];
}
