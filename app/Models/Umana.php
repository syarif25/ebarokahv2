<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Umana extends Authenticatable
{
    protected $table = 'umana';
    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    /**
     * Get formatted full name with degrees
     */
    public function getNamaGelarAttribute()
    {
        $depan = trim($this->gelar_depan ?? '');
        $nama = trim($this->nama_lengkap ?? '');
        $belakang = trim($this->gelar_belakang ?? '');

        $result = $depan ? $depan . ' ' . $nama : $nama;
        if ($belakang) {
            $result .= ', ' . $belakang;
        }
        return trim($result) ?: '-';
    }
}
