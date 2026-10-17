<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penempatan extends Model
{
    protected $table = 'penempatan';
    protected $primaryKey = 'id_penempatan';
    public $timestamps = false;
    protected $guarded = [];

    public function umana()
    {
        return $this->belongsTo(Umana::class, 'nik', 'nik');
    }

    public function ketentuan()
    {
        return $this->belongsTo(KetentuanBarokah::class, 'id_ketentuan', 'id_ketentuan');
    }

    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class, 'id_lembaga', 'id_lembaga');
    }

    public function potongans()
    {
        return $this->hasMany(PotonganUmana::class, 'id_penempatan', 'id_penempatan');
    }
}
