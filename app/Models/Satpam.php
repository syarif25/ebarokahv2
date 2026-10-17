<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satpam extends Model
{
    protected $table = 'satpam';
    protected $primaryKey = 'id_satpam';
    public $timestamps = false;
    protected $guarded = [];

    public function umana()
    {
        return $this->belongsTo(Umana::class, 'nik', 'nik');
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'id_transport', 'id_transport');
    }

    public function potongans()
    {
        return $this->hasMany(PotonganSatpam::class, 'id_satpam', 'id_satpam');
    }
}
