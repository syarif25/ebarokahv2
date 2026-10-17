<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KetentuanBarokah extends Model
{
    protected $table = 'ketentuan_barokah';
    protected $primaryKey = 'id_ketentuan';
    public $timestamps = false;
    protected $guarded = [];
}
