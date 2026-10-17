<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Umana extends Model
{
    protected $table = 'umana';
    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];
}
