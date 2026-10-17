<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tunkel extends Model
{
    protected $table = 'tunkel';
    protected $primaryKey = 'id_tunkel';
    public $timestamps = false;
    protected $guarded = [];
}
