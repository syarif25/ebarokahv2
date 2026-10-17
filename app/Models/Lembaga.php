<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lembaga extends Model
{
    protected $table = 'lembaga';
    protected $primaryKey = 'id_lembaga';
    public $timestamps = false;
    protected $guarded = [];
}
