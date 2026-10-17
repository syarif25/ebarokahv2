<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiburPesantren extends Model
{
    protected $table = 'libur_pesantren';
    protected $primaryKey = 'id_libur';
    public $timestamps = false;
    protected $guarded = [];
}
