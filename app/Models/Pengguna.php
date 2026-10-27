<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Pengguna extends Authenticatable
{
    use HasRoles;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false;
    protected $guarded = [];

    public function lembagaData()
    {
        return $this->belongsTo(Lembaga::class, 'lembaga', 'id_lembaga');
    }

    /**
     * Cek apakah pengguna adalah SuperAdmin
     */
    public function isSuperAdmin(): bool
    {
        $role = strtolower(str_replace(' ', '', (string)$this->jabatan));
        return in_array($role, ['superadmin', 'admin', 'root']);
    }

    /**
     * Cek apakah pengguna adalah Evaluasi
     */
    public function isEvaluasi(): bool
    {
        $role = strtolower(str_replace(' ', '', (string)$this->jabatan));
        return $role === 'evaluasi';
    }

    /**
     * Cek apakah pengguna adalah Admin Lembaga
     */
    public function isAdminLembaga(): bool
    {
        $role = strtolower(str_replace(' ', '', (string)$this->jabatan));
        return in_array($role, ['adminlembaga', 'lembaga']);
    }

    /**
     * Cek apakah pengguna adalah SDM
     */
    public function isSDM(): bool
    {
        $role = strtolower(str_replace(' ', '', (string)$this->jabatan));
        return $role === 'sdm';
    }

    /**
     * Cek apakah pengguna adalah Kasir
     */
    public function isKasir(): bool
    {
        $role = strtolower(str_replace(' ', '', (string)$this->jabatan));
        return $role === 'kasir';
    }

    /**
     * Cek apakah pengguna adalah akun khusus Satpam
     */
    public function isSatpam(): bool
    {
        if (strtolower(trim((string)$this->username)) === 'satpam') {
            return true;
        }
        if ((string)$this->lembaga === '59') {
            return true;
        }
        if ($this->lembagaData && strtolower(trim((string)$this->lembagaData->nama_lembaga)) === 'satpam') {
            return true;
        }
        return false;
    }

    /**
     * Cek apakah lembaga pengguna memiliki tenaga pengajar (guru/dosen)
     */
    public function hasTenagaPengajar(): bool
    {
        if ($this->isSuperAdmin() || $this->isEvaluasi()) {
            return true;
        }

        if ($this->lembagaData) {
            $tp = strtolower(trim((string)$this->lembagaData->tenaga_pengajar));
            return in_array($tp, ['ya', 'iya', '1', 'true']);
        }

        return false;
    }

    /**
     * Hak akses menu Kehadiran Struktural
     */
    public function canAccessKehadiranStruktural(): bool
    {
        return $this->isSuperAdmin() || $this->isEvaluasi() || $this->isAdminLembaga();
    }

    /**
     * Hak akses menu Kehadiran Pengajar
     */
    public function canAccessKehadiranPengajar(): bool
    {
        if ($this->isSuperAdmin() || $this->isEvaluasi()) {
            return true;
        }

        if ($this->isAdminLembaga()) {
            return $this->hasTenagaPengajar();
        }

        return false;
    }

    /**
     * Hak akses menu Kehadiran Satpam
     */
    public function canAccessKehadiranSatpam(): bool
    {
        if ($this->isSuperAdmin() || $this->isEvaluasi()) {
            return true;
        }

        return $this->isSatpam();
    }

    /**
     * Hak akses menu Validasi Barokah
     */
    public function canAccessValidasi(): bool
    {
        return $this->isSuperAdmin() || $this->isEvaluasi();
    }

    /**
     * Hak akses menu Laporan
     */
    public function canAccessLaporan(): bool
    {
        return $this->isSuperAdmin() || $this->isEvaluasi();
    }

    /**
     * Hak akses menu Master Data, PayRoll, dan Log
     */
    public function canAccessAdminTools(): bool
    {
        return $this->isSuperAdmin();
    }
}
