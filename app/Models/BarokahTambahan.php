<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarokahTambahan extends Model
{
    use HasFactory;

    protected $table = 'barokah_tambahan';
    protected $primaryKey = 'id_barokah_tambahan';
    public $timestamps = false;

    protected $fillable = [
        'id_pengajar',
        'nama_barokah',
        'nominal_tambahan',
        'min_periode_tambahan',
        'max_periode_tambahan'
    ];

    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class, 'id_pengajar', 'id_pengajar');
    }
}
