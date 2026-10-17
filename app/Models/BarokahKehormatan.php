<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarokahKehormatan extends Model
{
    protected $table = 'barokah_kehormatan';
    protected $primaryKey = 'id_bkehormatan';
    public $timestamps = false;
    protected $guarded = [];
}
