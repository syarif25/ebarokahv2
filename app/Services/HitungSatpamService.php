<?php

namespace App\Services;

use App\Models\KehadiranLembaga;
use App\Models\KehadiranSatpam;
use App\Models\TotalBarokahSatpam;
use Illuminate\Support\Facades\DB;

class HitungSatpamService
{
    /**
     * Ambil tarif acuan satpam
     */
    public static function getTarif()
    {
        $honorDanru = 100000;
        $qDanru = DB::table('master_honor_satpam')->where('nama_honor', 'Danru')->first();
        if ($qDanru && isset($qDanru->nominal)) {
            $honorDanru = (int)$qDanru->nominal;
        }

        return [
            'rank'       => 17500,  // Honor per shift
            'konsumsi'   => 5000,   // Konsumsi per dinihari
            'honorDanru' => $honorDanru,
        ];
    }

    /**
     * Hitung rincian barokah per baris satpam
     */
    public static function calculateRow($row, $tarif = null)
    {
        if (!$tarif) {
            $tarif = self::getTarif();
        }

        $jHari   = (int)($row->jumlah_hari ?? 0);
        $jShift  = (int)($row->jumlah_shift ?? 0);
        $jDini   = (int)($row->jumlah_dinihari ?? 0);
        $nTrans  = (int)($row->nominal_transport ?? 0);
        $isDanru = (isset($row->is_danru) && (string)$row->is_danru === '1');
        $nDanru  = $isDanru ? $tarif['honorDanru'] : 0;
        $pot     = (int)($row->nominal_potongan ?? $row->potongan ?? 0);

        $jTrans = $jHari * $nTrans;
        $jBar   = $jShift * $tarif['rank'];
        $jKons  = $jDini * $tarif['konsumsi'];
        $diter  = $jTrans + $jBar + $jKons + $nDanru - $pot;

        return [
            'jumlah_hari'      => $jHari,
            'nominal_transport'=> $nTrans,
            'jumlah_transport' => $jTrans,
            'jumlah_shift'     => $jShift,
            'rank'             => $tarif['rank'],
            'jumlah_barokah'   => $jBar,
            'jumlah_dinihari'  => $jDini,
            'konsumsi'         => $tarif['konsumsi'],
            'jumlah_konsumsi'  => $jKons,
            'is_danru'         => $isDanru,
            'nominal_danru'    => $nDanru,
            'nominal_potongan' => $pot,
            'diterima'         => $diter,
        ];
    }

    /**
     * Hitung total agregat untuk periode
     */
    public static function calculateTotals($idKehadiranLembaga)
    {
        $tarif = self::getTarif();

        $sql = "
            SELECT
                COALESCE(SUM(ks.jumlah_hari * COALESCE(t.nominal_transport, 0)), 0) AS total_transport,
                COALESCE(SUM(ks.jumlah_shift * ?), 0)                                AS total_barokah,
                COALESCE(SUM(ks.jumlah_dinihari * ?), 0)                             AS total_dinihari,
                COALESCE(SUM(CASE WHEN s.is_danru = '1' THEN ? ELSE 0 END), 0)      AS total_danru,
                COALESCE(SUM((SELECT COALESCE(SUM(nominal_potongan), 0) FROM potongan_satpam WHERE id_satpam = ks.id_satpam AND (max_periode_potongan >= CURDATE() OR max_periode_potongan IS NULL OR max_periode_potongan = '0000-00-00'))), 0) AS total_potongan
            FROM kehadiran_satpam ks
            JOIN satpam s ON s.id_satpam = ks.id_satpam
            LEFT JOIN transport t ON t.id_transport = s.id_transport
            WHERE ks.id_kehadiran_lembaga = ?
        ";

        $res = DB::selectOne($sql, [
            $tarif['rank'],
            $tarif['konsumsi'],
            $tarif['honorDanru'],
            $idKehadiranLembaga
        ]);

        $grand = ((int)$res->total_transport) + ((int)$res->total_barokah) + ((int)$res->total_dinihari) + ((int)$res->total_danru) - ((int)$res->total_potongan);

        return [
            'total_transport' => (int)$res->total_transport,
            'total_barokah'   => (int)$res->total_barokah,
            'total_dinihari'  => (int)$res->total_dinihari,
            'total_danru'     => (int)$res->total_danru,
            'total_potongan'  => (int)$res->total_potongan,
            'grand_total'     => $grand,
        ];
    }

    /**
     * Generate snapshot ke tabel total_barokah_satpam
     */
    public static function generateSnapshot($idKehadiranLembaga)
    {
        $tarif = self::getTarif();

        $sql = "
            SELECT 
                ks.*, 
                s.is_danru, 
                t.nominal_transport,
                (SELECT COALESCE(SUM(nominal_potongan), 0) FROM potongan_satpam WHERE id_satpam = ks.id_satpam AND (max_periode_potongan >= CURDATE() OR max_periode_potongan IS NULL OR max_periode_potongan = '0000-00-00')) AS nominal_potongan
            FROM kehadiran_satpam ks
            JOIN satpam s ON s.id_satpam = ks.id_satpam
            LEFT JOIN transport t ON t.id_transport = s.id_transport
            WHERE ks.id_kehadiran_lembaga = ?
            ORDER BY ks.id_kehadiran_satpam ASC
        ";

        $rows = DB::select($sql, [$idKehadiranLembaga]);

        if (empty($rows)) {
            throw new \Exception('Data kehadiran satpam tidak ditemukan.');
        }

        $snapshot = [];
        $grandTotal = 0;

        foreach ($rows as $r) {
            $calc = self::calculateRow($r, $tarif);
            $grandTotal += $calc['diterima'];

            $snapshot[] = [
                'id_satpam'            => $r->id_satpam,
                'id_kehadiran_satpam'  => $r->id_kehadiran_satpam,
                'id_kehadiran_lembaga' => $idKehadiranLembaga,
                'bulan'                => $r->bulan,
                'tahun'                => $r->tahun,
                'jumlah_hari'          => $calc['jumlah_hari'],
                'nominal_transport'    => $calc['nominal_transport'],
                'jumlah_transport'     => $calc['jumlah_transport'],
                'jumlah_shift'         => $calc['jumlah_shift'],
                'rank'                 => $calc['rank'],
                'jumlah_barokah'       => $calc['jumlah_barokah'],
                'jumlah_dinihari'      => $calc['jumlah_dinihari'],
                'konsumsi'             => $calc['konsumsi'],
                'jumlah_konsumsi'      => $calc['jumlah_konsumsi'],
                'nominal_danru'        => $calc['nominal_danru'],
                'potongan'             => $calc['nominal_potongan'],
                'diterima'             => $calc['diterima'],
                'status'               => 'selesai',
                'tgl_input'            => now(),
            ];
        }

        DB::beginTransaction();
        try {
            DB::table('total_barokah_satpam')->where('id_kehadiran_lembaga', $idKehadiranLembaga)->delete();
            DB::table('total_barokah_satpam')->insert($snapshot);

            DB::table('kehadiran_lembaga')->where('id_kehadiran_lembaga', $idKehadiranLembaga)->update([
                'status'       => 'acc',
                'jumlah_total' => $grandTotal,
            ]);

            DB::commit();
            return [
                'status'     => true,
                'grand_total'=> $grandTotal,
                'count'      => count($snapshot)
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
