<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TunjAnak extends Model
{
    protected $table = 'tunjanak';
    protected $primaryKey = 'id_tunj_anak';
    public $timestamps = false;
    protected $guarded = [];
}
