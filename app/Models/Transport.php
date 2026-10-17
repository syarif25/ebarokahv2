<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $table = 'transport';
    protected $primaryKey = 'id_transport';
    public $timestamps = false;
    protected $guarded = [];
}
