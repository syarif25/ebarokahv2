<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'bank';
    protected $primaryKey = 'id_bank';
    protected $fillable = [
        'nama_bank',
        'kode_bank',
        'bifast_code',
        'tipe_transfer',
        'biaya_admin',
        'is_default'
    ];
}
