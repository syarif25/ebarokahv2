<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TBebanKerja extends Model
{
    protected $table = 't_beban_kerja';
    protected $primaryKey = 'id_tbk';
    public $timestamps = false;
    protected $guarded = [];

    public function penempatan()
    {
        return $this->belongsTo(Penempatan::class, 'id_penempatan', 'id_penempatan');
    }
}
