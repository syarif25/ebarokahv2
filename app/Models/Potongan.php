<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Potongan extends Model
{
    protected $table = 'potongan';
    protected $primaryKey = 'id_potongan';
    public $timestamps = false;
    protected $guarded = [];
}
