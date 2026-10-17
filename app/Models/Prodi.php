<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $table = 'master_prodi';
    protected $primaryKey = 'id_prodi';
    public $timestamps = false;
    protected $guarded = [];

    public function lembagaData()
    {
        return $this->belongsTo(Lembaga::class, 'id_lembaga', 'id_lembaga');
    }
}
