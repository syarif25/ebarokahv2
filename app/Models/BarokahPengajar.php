<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarokahPengajar extends Model
{
    protected $table = 'barokah_pengajar';
    protected $primaryKey = 'id_barokah_pengajar';
    public $timestamps = false;
    protected $guarded = [];
}
