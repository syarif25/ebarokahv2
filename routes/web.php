<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\LembagaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rute aplikasi E-Barokah V2 dengan proteksi Role-Based Access Control (RBAC).
|
*/

// Auth Routes (Public dengan Rate Limiting)
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authed Routes Group
Route::middleware(['auth'])->group(function () {

    // Dashboard (Dapat diakses seluruh akun yang telah login)
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    });

    /*
    |--------------------------------------------------------------------------
    | KEHADIRAN STRUKTURAL (SuperAdmin, Evaluasi, AdminLembaga)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:SuperAdmin,Evaluasi,AdminLembaga'])->group(function () {
        Route::get('/kehadiran_struktural', [App\Http\Controllers\KehadiranStrukturalController::class, 'index']);
        Route::post('/kehadiran_struktural/storeBlanko', [App\Http\Controllers\KehadiranStrukturalController::class, 'storeBlanko']);
        Route::get('/kehadiran_struktural/{id}/rekap', [App\Http\Controllers\KehadiranStrukturalController::class, 'rekap']);
        Route::post('/kehadiran_struktural/saveRekap', [App\Http\Controllers\KehadiranStrukturalController::class, 'saveRekap']);
        Route::get('/kehadiran_struktural/{id}/koreksi', [App\Http\Controllers\KehadiranStrukturalController::class, 'koreksi']);
        Route::post('/kehadiran_struktural/updateRow', [App\Http\Controllers\KehadiranStrukturalController::class, 'updateRow']);
        Route::get('/kehadiran_struktural/detailTbk/{id_penempatan}', [App\Http\Controllers\KehadiranStrukturalController::class, 'detailTbk']);
        Route::post('/kehadiran_struktural/saveTbk', [App\Http\Controllers\KehadiranStrukturalController::class, 'saveTbk']);
        Route::get('/kehadiran_struktural/getPotonganOptions', [App\Http\Controllers\KehadiranStrukturalController::class, 'getPotonganOptions']);
        Route::get('/kehadiran_struktural/detailPotongan/{id_penempatan}/{periode_date?}', [App\Http\Controllers\KehadiranStrukturalController::class, 'detailPotongan']);
        Route::post('/kehadiran_struktural/savePotongan', [App\Http\Controllers\KehadiranStrukturalController::class, 'savePotongan']);
        Route::post('/kehadiran_struktural/kirim', [App\Http\Controllers\KehadiranStrukturalController::class, 'kirim']);
        Route::post('/kehadiran_struktural/validasi', [App\Http\Controllers\KehadiranStrukturalController::class, 'validasi']);
        Route::get('/kehadiran_struktural/riwayat/{id}', [App\Http\Controllers\KehadiranStrukturalController::class, 'riwayat']);
        Route::post('/kehadiran_struktural/reset', [App\Http\Controllers\KehadiranStrukturalController::class, 'reset']);
        Route::get('/kehadiran_struktural/cekValidasiUmana/{nik}', [App\Http\Controllers\KehadiranStrukturalController::class, 'cekValidasiUmana']);
        Route::get('/kehadiran_struktural/cetak/{id}', [App\Http\Controllers\KehadiranStrukturalController::class, 'cetak']);
        Route::get('/kehadiran_struktural/cetak_potongan/{id}/{id_kehadiran?}', [App\Http\Controllers\KehadiranStrukturalController::class, 'cetakPotongan']);
    });

    /*
    |--------------------------------------------------------------------------
    | KEHADIRAN PENGAJAR (SuperAdmin, Evaluasi, & AdminLembaga Ber-Pengajar)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:SuperAdmin,Evaluasi,AdminLembaga', 'akses.pengajar'])->group(function () {
        Route::get('/kehadiran_pengajar', [App\Http\Controllers\KehadiranPengajarController::class, 'index']);
        Route::post('/kehadiran_pengajar/storeBlanko', [App\Http\Controllers\KehadiranPengajarController::class, 'storeBlanko']);
        Route::get('/kehadiran_pengajar/{id}/rekap', [App\Http\Controllers\KehadiranPengajarController::class, 'rekap']);
        Route::post('/kehadiran_pengajar/saveRekap', [App\Http\Controllers\KehadiranPengajarController::class, 'saveRekap']);
        Route::get('/kehadiran_pengajar/{id}/koreksi', [App\Http\Controllers\KehadiranPengajarController::class, 'koreksi']);
        Route::post('/kehadiran_pengajar/updateRow', [App\Http\Controllers\KehadiranPengajarController::class, 'updateRow']);
        Route::post('/kehadiran_pengajar/kirim', [App\Http\Controllers\KehadiranPengajarController::class, 'kirim']);
        Route::post('/kehadiran_pengajar/validasi', [App\Http\Controllers\KehadiranPengajarController::class, 'validasi']);
        Route::post('/kehadiran_pengajar/syncPengajar', [App\Http\Controllers\KehadiranPengajarController::class, 'syncPengajar']);
        Route::get('/kehadiran_pengajar/detailPotongan/{id_pengajar}/{periode_date?}', [App\Http\Controllers\KehadiranPengajarController::class, 'detailPotongan']);
        Route::post('/kehadiran_pengajar/savePotongan', [App\Http\Controllers\KehadiranPengajarController::class, 'savePotongan']);
        Route::post('/kehadiran_pengajar/saveCatatan', [App\Http\Controllers\KehadiranPengajarController::class, 'saveCatatan']);
        Route::get('/kehadiran_pengajar/riwayat/{id}', [App\Http\Controllers\KehadiranPengajarController::class, 'riwayat']);
        Route::post('/kehadiran_pengajar/reset', [App\Http\Controllers\KehadiranPengajarController::class, 'reset']);
        Route::get('/kehadiran_pengajar/cetak/{id}', [App\Http\Controllers\KehadiranPengajarController::class, 'cetak']);
        Route::get('/kehadiran_pengajar/cetak_potongan/{id}/{id_kehadiran?}', [App\Http\Controllers\KehadiranPengajarController::class, 'cetakPotongan']);
        Route::get('/kehadiran_pengajar/cekValidasiUmana/{nik}', [App\Http\Controllers\KehadiranStrukturalController::class, 'cekValidasiUmana']);
    });

    /*
    |--------------------------------------------------------------------------
    | KEHADIRAN SATPAM (SuperAdmin, Evaluasi, & Khusus Akun Satpam)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:SuperAdmin,Evaluasi,AdminLembaga', 'akses.satpam'])->group(function () {
        Route::get('/kehadiran-satpam', [App\Http\Controllers\KehadiranSatpamController::class, 'index']);
        Route::get('/kehadiran_satpam', [App\Http\Controllers\KehadiranSatpamController::class, 'index']);
        Route::get('/kehadiran_satpam/data_list', [App\Http\Controllers\KehadiranSatpamController::class, 'index']);
        Route::post('/kehadiran_satpam/data_list', [App\Http\Controllers\KehadiranSatpamController::class, 'index']);
        Route::post('/kehadiran-satpam/blanko', [App\Http\Controllers\KehadiranSatpamController::class, 'storeBlanko']);
        Route::post('/kehadiran_satpam/blanko_add', [App\Http\Controllers\KehadiranSatpamController::class, 'storeBlanko']);
        Route::get('/kehadiran-satpam/{id}/rekap', [App\Http\Controllers\KehadiranSatpamController::class, 'rekap']);
        Route::get('/kehadiran_satpam/add_kehadiran/{id}', [App\Http\Controllers\KehadiranSatpamController::class, 'rekap']);
        Route::post('/kehadiran-satpam/rekap', [App\Http\Controllers\KehadiranSatpamController::class, 'saveRekap']);
        Route::post('/kehadiran_satpam/ajax_add_kehadiran', [App\Http\Controllers\KehadiranSatpamController::class, 'saveRekap']);
        Route::get('/kehadiran-satpam/{id}/koreksi', [App\Http\Controllers\KehadiranSatpamController::class, 'koreksi']);
        Route::get('/validasi_satpam/koreksi_satpam/{id}', [App\Http\Controllers\KehadiranSatpamController::class, 'koreksi']);
        Route::post('/kehadiran-satpam/update-row', [App\Http\Controllers\KehadiranSatpamController::class, 'updateRow']);
        Route::post('/validasi_satpam/update_row', [App\Http\Controllers\KehadiranSatpamController::class, 'updateRow']);
        Route::post('/kehadiran-satpam/kirim', [App\Http\Controllers\KehadiranSatpamController::class, 'kirim']);
        Route::post('/validasi_satpam/update_kirim', [App\Http\Controllers\KehadiranSatpamController::class, 'kirim']);
        Route::post('/kehadiran-satpam/validasi', [App\Http\Controllers\KehadiranSatpamController::class, 'validasi']);
        Route::post('/validasi_satpam/save_data', [App\Http\Controllers\KehadiranSatpamController::class, 'validasi']);
        Route::post('/kehadiran-satpam/reset', [App\Http\Controllers\KehadiranSatpamController::class, 'reset']);
        Route::post('/kehadiran_satpam/reset_json', [App\Http\Controllers\KehadiranSatpamController::class, 'reset']);
        Route::get('/kehadiran-satpam/riwayat/{id}', [App\Http\Controllers\KehadiranSatpamController::class, 'riwayat']);
        Route::get('/kehadiran-satpam/{id}/cetak', [App\Http\Controllers\KehadiranSatpamController::class, 'cetak']);
        Route::get('/validasi_satpam/cetak/{id}', [App\Http\Controllers\KehadiranSatpamController::class, 'cetak']);
        Route::get('/Laporan_satpam/cetak/{id}', [App\Http\Controllers\KehadiranSatpamController::class, 'cetak']);
        Route::get('/kehadiran-satpam/{id}/rincian', [App\Http\Controllers\KehadiranSatpamController::class, 'rincian']);
        Route::get('/Laporan_satpam/rincian/{id}', [App\Http\Controllers\KehadiranSatpamController::class, 'rincian']);
        Route::get('/kehadiran-satpam/getPotonganOptions', [App\Http\Controllers\KehadiranSatpamController::class, 'getPotonganOptions']);
        Route::get('/Validasi_satpam/get_potongan_options', [App\Http\Controllers\KehadiranSatpamController::class, 'getPotonganOptions']);
        Route::get('/kehadiran-satpam/detailPotongan/{id_satpam}', [App\Http\Controllers\KehadiranSatpamController::class, 'detailPotongan']);
        Route::get('/Validasi_satpam/detail_potongan/{id_satpam}', [App\Http\Controllers\KehadiranSatpamController::class, 'detailPotongan']);
        Route::post('/kehadiran-satpam/savePotongan', [App\Http\Controllers\KehadiranSatpamController::class, 'savePotongan']);
        Route::post('/Validasi_satpam/save_potongan', [App\Http\Controllers\KehadiranSatpamController::class, 'savePotongan']);
        Route::get('/laporan/satpam', [App\Http\Controllers\KehadiranSatpamController::class, 'index']);
    });

    /*
    |--------------------------------------------------------------------------
    | VALIDASI BAROKAH & LAPORAN (SuperAdmin & Evaluasi)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:SuperAdmin,Evaluasi'])->group(function () {
        // Validasi Barokah
        Route::get('/validasi', [App\Http\Controllers\ValidasiController::class, 'index']);
        Route::get('/Validasi', [App\Http\Controllers\ValidasiController::class, 'index']);
        Route::get('/validasi/data', [App\Http\Controllers\ValidasiController::class, 'dataList']);

        // Audit Tunjangan Ganda
        Route::get('/validasi/audit-tunjangan', [App\Http\Controllers\AuditTunjanganController::class, 'index']);
        Route::get('/audit-tunjangan', [App\Http\Controllers\AuditTunjanganController::class, 'index']);
        Route::get('/validasi/audit-tunjangan/data', [App\Http\Controllers\AuditTunjanganController::class, 'data']);
        Route::get('/validasi/audit-tunjangan/detail/{nik}', [App\Http\Controllers\AuditTunjanganController::class, 'detail']);
        Route::get('/validasi/audit-tunjangan/export-excel', [App\Http\Controllers\AuditTunjanganController::class, 'exportExcel']);

        // Laporan Per Lembaga
        Route::get('/laporan/per-lembaga', [App\Http\Controllers\LaporanLembagaController::class, 'index']);
        Route::get('/Laporan_lembaga', function() { return redirect('/laporan/per-lembaga'); });
        Route::get('/Laporan_lembaga/rincian/{id}', function($id) { return redirect('/laporan/per-lembaga/rincian/' . $id); });
        Route::post('/laporan/per-lembaga/data', [App\Http\Controllers\LaporanLembagaController::class, 'data']);
        Route::get('/laporan/per-lembaga/rincian/{id}', [App\Http\Controllers\LaporanLembagaController::class, 'rincian']);
        Route::get('/laporan/per-lembaga/cetak/{id}', [App\Http\Controllers\LaporanLembagaController::class, 'cetak']);
        Route::get('/laporan/per-lembaga/export-excel/{id}', [App\Http\Controllers\LaporanLembagaController::class, 'exportExcel']);

        // Rekap Kehadiran Pengajar
        Route::get('/laporan/rekap-kehadiran-pengajar', [App\Http\Controllers\RekapKehadiranPengajarController::class, 'index']);
        Route::get('/Rekap_kehadiran_pengajar', function() { return redirect('/laporan/rekap-kehadiran-pengajar'); });
        Route::post('/laporan/rekap-kehadiran-pengajar/get-data', [App\Http\Controllers\RekapKehadiranPengajarController::class, 'getData']);
        Route::post('/Rekap_kehadiran_pengajar/get_data', [App\Http\Controllers\RekapKehadiranPengajarController::class, 'getData']);

        // Laporan Per Umana
        Route::get('/laporan/per-umana', [App\Http\Controllers\LaporanPerUmanaController::class, 'index']);
        Route::get('/Laporan/per_umana', function() { return redirect('/laporan/per-umana'); });
        Route::get('/Laporan_v2/per_umana', function() { return redirect('/laporan/per-umana'); });
        Route::post('/laporan/per-umana/data', [App\Http\Controllers\LaporanPerUmanaController::class, 'data']);
        Route::get('/laporan/per-umana/rincian/{nik}', [App\Http\Controllers\LaporanPerUmanaController::class, 'rincian']);
        Route::get('/laporan/per-umana/breakdown', [App\Http\Controllers\LaporanPerUmanaController::class, 'breakdown']);

        // Laporan Waktu Pengajuan
        Route::get('/laporan/waktu-pengajuan', [App\Http\Controllers\LaporanWaktuPengajuanController::class, 'index']);
        Route::get('/Laporan_waktu', function() { return redirect('/laporan/waktu-pengajuan'); });
        Route::post('/laporan/waktu-pengajuan/data', [App\Http\Controllers\LaporanWaktuPengajuanController::class, 'data']);
        Route::post('/laporan_waktu/data_list', [App\Http\Controllers\LaporanWaktuPengajuanController::class, 'data']);
        Route::get('/laporan/waktu-pengajuan/export-excel', [App\Http\Controllers\LaporanWaktuPengajuanController::class, 'exportExcel']);
    });

    /*
    |--------------------------------------------------------------------------
    | MASTER DATA, MANAJEMEN, PAYROLL & LOG (Khusus SuperAdmin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:SuperAdmin'])->group(function () {
        // Master Data
        Route::get('/master-data/lembaga', [LembagaController::class, 'index']);
        Route::post('/master-data/lembaga/store', [LembagaController::class, 'store']);
        Route::post('/master-data/lembaga/update/{id}', [LembagaController::class, 'update']);
        Route::post('/master-data/lembaga/delete/{id}', [LembagaController::class, 'destroy']);

        Route::get('/master-data/prodi', [App\Http\Controllers\ProdiController::class, 'index']);
        Route::post('/master-data/prodi/store', [App\Http\Controllers\ProdiController::class, 'store']);
        Route::post('/master-data/prodi/update/{id}', [App\Http\Controllers\ProdiController::class, 'update']);
        Route::post('/master-data/prodi/delete/{id}', [App\Http\Controllers\ProdiController::class, 'destroy']);

        Route::get('/master-data/pengguna', [PenggunaController::class, 'index']);
        Route::post('/master-data/pengguna/store', [PenggunaController::class, 'store']);
        Route::post('/master-data/pengguna/update/{id}', [PenggunaController::class, 'update']);
        Route::post('/master-data/pengguna/delete/{id}', [PenggunaController::class, 'destroy']);

        Route::get('/master-data/umana', [App\Http\Controllers\UmanaController::class, 'index']);
        Route::get('/master-data/umana/edit/{id}', [App\Http\Controllers\UmanaController::class, 'edit']);
        Route::post('/master-data/umana/store', [App\Http\Controllers\UmanaController::class, 'store']);
        Route::post('/master-data/umana/update/{id}', [App\Http\Controllers\UmanaController::class, 'update']);
        Route::post('/master-data/umana/delete/{id}', [App\Http\Controllers\UmanaController::class, 'destroy']);

        Route::get('/master-data/tahun-acuan', [\App\Http\Controllers\PengaturanTahunAcuanController::class, 'index']);
        Route::post('/master-data/tahun-acuan/store', [\App\Http\Controllers\PengaturanTahunAcuanController::class, 'store']);
        Route::post('/master-data/tahun-acuan/update/{id}', [\App\Http\Controllers\PengaturanTahunAcuanController::class, 'update']);
        Route::post('/master-data/tahun-acuan/delete/{id}', [\App\Http\Controllers\PengaturanTahunAcuanController::class, 'destroy']);

        Route::get('/master-data/ketentuan-barokah', [App\Http\Controllers\KetentuanBarokahController::class, 'index']);
        Route::post('/master-data/ketentuan-barokah/store', [App\Http\Controllers\KetentuanBarokahController::class, 'store']);
        Route::post('/master-data/ketentuan-barokah/update/{id}', [App\Http\Controllers\KetentuanBarokahController::class, 'update']);
        Route::post('/master-data/ketentuan-barokah/delete/{id}', [App\Http\Controllers\KetentuanBarokahController::class, 'destroy']);

        Route::get('/master-data/libur-pesantren', [App\Http\Controllers\LiburPesantrenController::class, 'index']);
        Route::post('/master-data/libur-pesantren/store', [App\Http\Controllers\LiburPesantrenController::class, 'store']);
        Route::post('/master-data/libur-pesantren/update/{id}', [App\Http\Controllers\LiburPesantrenController::class, 'update']);
        Route::post('/master-data/libur-pesantren/delete/{id}', [App\Http\Controllers\LiburPesantrenController::class, 'destroy']);

        Route::get('/master-data/jenis-potongan', [App\Http\Controllers\PotonganController::class, 'index']);
        Route::post('/master-data/jenis-potongan/store', [App\Http\Controllers\PotonganController::class, 'store']);
        Route::post('/master-data/jenis-potongan/update/{id}', [App\Http\Controllers\PotonganController::class, 'update']);
        Route::post('/master-data/jenis-potongan/delete/{id}', [App\Http\Controllers\PotonganController::class, 'destroy']);

        Route::get('/master-data/tunkel', [App\Http\Controllers\TunkelController::class, 'index']);
        Route::post('/master-data/tunkel/store', [App\Http\Controllers\TunkelController::class, 'store']);
        Route::post('/master-data/tunkel/update/{id}', [App\Http\Controllers\TunkelController::class, 'update']);
        Route::post('/master-data/tunkel/delete/{id}', [App\Http\Controllers\TunkelController::class, 'destroy']);

        Route::get('/master-data/tunj-anak', [App\Http\Controllers\TunjAnakController::class, 'index']);
        Route::post('/master-data/tunj-anak/store', [App\Http\Controllers\TunjAnakController::class, 'store']);
        Route::post('/master-data/tunj-anak/update/{id}', [App\Http\Controllers\TunjAnakController::class, 'update']);
        Route::post('/master-data/tunj-anak/delete/{id}', [App\Http\Controllers\TunjAnakController::class, 'destroy']);

        Route::get('/master-data/transport', [App\Http\Controllers\TransportController::class, 'index']);
        Route::post('/master-data/transport/store', [App\Http\Controllers\TransportController::class, 'store']);
        Route::post('/master-data/transport/update/{id}', [App\Http\Controllers\TransportController::class, 'update']);
        Route::post('/master-data/transport/delete/{id}', [App\Http\Controllers\TransportController::class, 'destroy']);

        Route::get('/master-data/barokah-pengajar', [App\Http\Controllers\BarokahPengajarController::class, 'index']);
        Route::post('/master-data/barokah-pengajar/store', [App\Http\Controllers\BarokahPengajarController::class, 'store']);
        Route::post('/master-data/barokah-pengajar/update/{id}', [App\Http\Controllers\BarokahPengajarController::class, 'update']);
        Route::post('/master-data/barokah-pengajar/delete/{id}', [App\Http\Controllers\BarokahPengajarController::class, 'destroy']);

        Route::get('/master-data/barokah-kehormatan', [App\Http\Controllers\BarokahKehormatanController::class, 'index']);
        Route::post('/master-data/barokah-kehormatan/store', [App\Http\Controllers\BarokahKehormatanController::class, 'store']);
        Route::post('/master-data/barokah-kehormatan/update/{id}', [App\Http\Controllers\BarokahKehormatanController::class, 'update']);
        Route::post('/master-data/barokah-kehormatan/delete/{id}', [App\Http\Controllers\BarokahKehormatanController::class, 'destroy']);

        Route::get('/master-data/barokah-kehormatan-pengajar', [App\Http\Controllers\BarokahKehormatanPengajarController::class, 'index']);
        Route::post('/master-data/barokah-kehormatan-pengajar/store', [App\Http\Controllers\BarokahKehormatanPengajarController::class, 'store']);
        Route::post('/master-data/barokah-kehormatan-pengajar/update/{id}', [App\Http\Controllers\BarokahKehormatanPengajarController::class, 'update']);
        Route::post('/master-data/barokah-kehormatan-pengajar/delete/{id}', [App\Http\Controllers\BarokahKehormatanPengajarController::class, 'destroy']);

        Route::get('/master-data/bank', [App\Http\Controllers\BankController::class, 'index']);
        Route::post('/master-data/bank/store', [App\Http\Controllers\BankController::class, 'store']);
        Route::post('/master-data/bank/update/{id}', [App\Http\Controllers\BankController::class, 'update']);
        Route::post('/master-data/bank/delete/{id}', [App\Http\Controllers\BankController::class, 'destroy']);

        // Manajemen Umana'
        Route::get('/manajemen-umana/penempatan', [App\Http\Controllers\PenempatanController::class, 'index']);
        Route::get('/manajemen-umana/penempatan/create', [App\Http\Controllers\PenempatanController::class, 'create']);
        Route::post('/manajemen-umana/penempatan/store', [App\Http\Controllers\PenempatanController::class, 'store']);
        Route::get('/manajemen-umana/penempatan/edit/{id}', [App\Http\Controllers\PenempatanController::class, 'edit']);
        Route::post('/manajemen-umana/penempatan/update/{id}', [App\Http\Controllers\PenempatanController::class, 'update']);
        Route::post('/manajemen-umana/penempatan/delete/{id}', [App\Http\Controllers\PenempatanController::class, 'destroy']);

        Route::get('/manajemen-umana/potongan-umana', [App\Http\Controllers\PotonganUmanaController::class, 'index']);
        Route::get('/manajemen-umana/potongan-umana/detail/{id}', [App\Http\Controllers\PotonganUmanaController::class, 'getDetail']);
        Route::post('/manajemen-umana/potongan-umana/store', [App\Http\Controllers\PotonganUmanaController::class, 'store']);
        Route::post('/manajemen-umana/potongan-umana/update/{id}', [App\Http\Controllers\PotonganUmanaController::class, 'update']);
        Route::post('/manajemen-umana/potongan-umana/delete/{id}', [App\Http\Controllers\PotonganUmanaController::class, 'destroy']);

        Route::get('/manajemen-umana/tunjangan-beban-kerja', [App\Http\Controllers\TBebanKerjaController::class, 'index']);
        Route::post('/manajemen-umana/tunjangan-beban-kerja/store', [App\Http\Controllers\TBebanKerjaController::class, 'store']);
        Route::post('/manajemen-umana/tunjangan-beban-kerja/update/{id}', [App\Http\Controllers\TBebanKerjaController::class, 'update']);

        // Manajemen Pengajar
        Route::get('/Penempatan/pengajar', [App\Http\Controllers\PengajarController::class, 'index']);
        Route::post('/Penempatan/pengajar/store', [App\Http\Controllers\PengajarController::class, 'store']);
        Route::post('/Penempatan/pengajar/update/{id}', [App\Http\Controllers\PengajarController::class, 'update']);
        Route::post('/Penempatan/pengajar/delete/{id}', [App\Http\Controllers\PengajarController::class, 'destroy']);
        Route::get('/Penempatan/pengajar/cekValidasiUmana/{nik}', [App\Http\Controllers\KehadiranStrukturalController::class, 'cekValidasiUmana']);
        
        Route::get('/Potongan_pengajar', [App\Http\Controllers\PotonganPengajarController::class, 'index']);
        Route::get('/Potongan_pengajar/detail/{id}', [App\Http\Controllers\PotonganPengajarController::class, 'getDetail']);
        Route::post('/Potongan_pengajar/store', [App\Http\Controllers\PotonganPengajarController::class, 'store']);
        Route::post('/Potongan_pengajar/update/{id}', [App\Http\Controllers\PotonganPengajarController::class, 'update']);
        Route::post('/Potongan_pengajar/delete/{id}', [App\Http\Controllers\PotonganPengajarController::class, 'destroy']);

        Route::get('/Barokah_tambahan', [App\Http\Controllers\BarokahTambahanController::class, 'index']);
        Route::get('/Barokah_tambahan/detail/{id}', [App\Http\Controllers\BarokahTambahanController::class, 'detail']);
        Route::post('/Barokah_tambahan/store', [App\Http\Controllers\BarokahTambahanController::class, 'store']);
        Route::post('/Barokah_tambahan/update/{id}', [App\Http\Controllers\BarokahTambahanController::class, 'update']);
        Route::post('/Barokah_tambahan/delete/{id}', [App\Http\Controllers\BarokahTambahanController::class, 'destroy']);

        // Manajemen Satpam
        Route::get('/manajemen-satpam/penempatan', [App\Http\Controllers\SatpamController::class, 'index']);
        Route::get('/Penempatan_satpam', [App\Http\Controllers\SatpamController::class, 'index']);
        Route::get('/manajemen-satpam/penempatan/get-umana/{nik}', [App\Http\Controllers\SatpamController::class, 'getUmana']);
        Route::post('/manajemen-satpam/penempatan/store', [App\Http\Controllers\SatpamController::class, 'store']);
        Route::get('/manajemen-satpam/penempatan/edit/{id}', [App\Http\Controllers\SatpamController::class, 'edit']);
        Route::post('/manajemen-satpam/penempatan/update/{id}', [App\Http\Controllers\SatpamController::class, 'update']);
        Route::post('/manajemen-satpam/penempatan/delete/{id}', [App\Http\Controllers\SatpamController::class, 'destroy']);

        Route::get('/manajemen-satpam/potongan-satpam', [App\Http\Controllers\PotonganSatpamController::class, 'index']);
        Route::get('/Potongan_satpam/per_satpam', [App\Http\Controllers\PotonganSatpamController::class, 'index']);
        Route::get('/Potongan_satpam', [App\Http\Controllers\PotonganSatpamController::class, 'index']);
        Route::post('/manajemen-satpam/potongan-satpam/store', [App\Http\Controllers\PotonganSatpamController::class, 'store']);
        Route::get('/manajemen-satpam/potongan-satpam/edit/{id}', [App\Http\Controllers\PotonganSatpamController::class, 'edit']);
        Route::post('/manajemen-satpam/potongan-satpam/update/{id}', [App\Http\Controllers\PotonganSatpamController::class, 'update']);
        Route::post('/manajemen-satpam/potongan-satpam/delete/{id}', [App\Http\Controllers\PotonganSatpamController::class, 'destroy']);

        // PayRoll
        Route::get('/payroll', [App\Http\Controllers\PayrollController::class, 'index']);
        Route::get('/Payroll', [App\Http\Controllers\PayrollController::class, 'index']);
        Route::get('/payroll/data', [App\Http\Controllers\PayrollController::class, 'dataList']);
        Route::get('/payroll/detail/{id}', [App\Http\Controllers\PayrollController::class, 'getById']);
        Route::post('/payroll/save', [App\Http\Controllers\PayrollController::class, 'save']);
        Route::get('/payroll/wa-log', [App\Http\Controllers\PayrollController::class, 'getWaLog']);
        Route::post('/payroll/resend-wa/{id}', [App\Http\Controllers\PayrollController::class, 'resendWa']);
        Route::post('/payroll/bulk-resend', [App\Http\Controllers\PayrollController::class, 'bulkResendFailed']);

        // Log Kehadiran Lembaga
        Route::get('/log/kehadiran-lembaga', [App\Http\Controllers\LogKehadiranLembagaController::class, 'index']);
        Route::get('/Log_kehadiran_lemb', function() { return redirect('/log/kehadiran-lembaga'); });
        Route::post('/log/kehadiran-lembaga/data', [App\Http\Controllers\LogKehadiranLembagaController::class, 'data']);
        Route::get('/log/kehadiran-lembaga/show/{id}', [App\Http\Controllers\LogKehadiranLembagaController::class, 'show']);
        Route::post('/log/kehadiran-lembaga/store', [App\Http\Controllers\LogKehadiranLembagaController::class, 'store']);
        Route::post('/log/kehadiran-lembaga/update/{id}', [App\Http\Controllers\LogKehadiranLembagaController::class, 'update']);
        Route::post('/log/kehadiran-lembaga/delete/{id}', [App\Http\Controllers\LogKehadiranLembagaController::class, 'destroy']);
        Route::get('/Log_kehadiran_lemb/hapus/{id}', function($id) {
            app(App\Http\Controllers\LogKehadiranLembagaController::class)->destroy($id);
            return redirect('/log/kehadiran-lembaga')->with('success', 'Data berhasil dihapus');
        });

        // Log Kehadiran Struktural
        Route::get('/log/kehadiran-struktural', [App\Http\Controllers\LogKehadiranStrukturalController::class, 'index']);
        Route::get('/Kehadiran/kehadiran_log', function() { return redirect('/log/kehadiran-struktural'); });
        Route::get('/kehadiran/kehadiran_log', function() { return redirect('/log/kehadiran-struktural'); });
        Route::post('/log/kehadiran-struktural/data', [App\Http\Controllers\LogKehadiranStrukturalController::class, 'data']);
        Route::post('/kehadiran/data_list_log', [App\Http\Controllers\LogKehadiranStrukturalController::class, 'data']);
        Route::get('/log/kehadiran-struktural/show/{id}', [App\Http\Controllers\LogKehadiranStrukturalController::class, 'show']);
        Route::post('/log/kehadiran-struktural/store', [App\Http\Controllers\LogKehadiranStrukturalController::class, 'store']);
        Route::post('/log/kehadiran-struktural/update/{id}', [App\Http\Controllers\LogKehadiranStrukturalController::class, 'update']);
        Route::match(['get', 'post', 'delete'], '/log/kehadiran-struktural/delete/{id}', [App\Http\Controllers\LogKehadiranStrukturalController::class, 'destroy']);
        Route::get('/log/kehadiran-struktural/penempatan-options', [App\Http\Controllers\LogKehadiranStrukturalController::class, 'searchPenempatan']);
        Route::match(['get', 'post', 'delete'], '/kehadiran/hapus/{id}', [App\Http\Controllers\LogKehadiranStrukturalController::class, 'destroy']);

        // Log Kehadiran Pengajar
        Route::get('/log/kehadiran-pengajar', [App\Http\Controllers\LogKehadiranPengajarController::class, 'index']);
        Route::post('/log/kehadiran-pengajar/data', [App\Http\Controllers\LogKehadiranPengajarController::class, 'data']);
        Route::get('/log/kehadiran-pengajar/show/{id}', [App\Http\Controllers\LogKehadiranPengajarController::class, 'show']);
        Route::post('/log/kehadiran-pengajar/store', [App\Http\Controllers\LogKehadiranPengajarController::class, 'store']);
        Route::post('/log/kehadiran-pengajar/update/{id}', [App\Http\Controllers\LogKehadiranPengajarController::class, 'update']);
        Route::match(['get', 'post', 'delete'], '/log/kehadiran-pengajar/delete/{id}', [App\Http\Controllers\LogKehadiranPengajarController::class, 'destroy']);
        Route::get('/log/kehadiran-pengajar/pengajar-options', [App\Http\Controllers\LogKehadiranPengajarController::class, 'searchPengajar']);

        // Log Potongan Pengajar
        Route::get('/log/potongan-pengajar', [App\Http\Controllers\LogPotonganPengajarController::class, 'index']);
        Route::post('/log/potongan-pengajar/data', [App\Http\Controllers\LogPotonganPengajarController::class, 'data']);
        Route::get('/log/potongan-pengajar/show/{id}', [App\Http\Controllers\LogPotonganPengajarController::class, 'show']);
        Route::post('/log/potongan-pengajar/store', [App\Http\Controllers\LogPotonganPengajarController::class, 'store']);
        Route::post('/log/potongan-pengajar/update/{id}', [App\Http\Controllers\LogPotonganPengajarController::class, 'update']);
        Route::match(['get', 'post', 'delete'], '/log/potongan-pengajar/delete/{id}', [App\Http\Controllers\LogPotonganPengajarController::class, 'destroy']);

        // Log Potongan Satpam
        Route::get('/log/potongan-satpam', [App\Http\Controllers\LogPotonganSatpamController::class, 'index']);
        Route::post('/log/potongan-satpam/data', [App\Http\Controllers\LogPotonganSatpamController::class, 'data']);
        Route::get('/log/potongan-satpam/show/{id}', [App\Http\Controllers\LogPotonganSatpamController::class, 'show']);
        Route::post('/log/potongan-satpam/store', [App\Http\Controllers\LogPotonganSatpamController::class, 'store']);
        Route::post('/log/potongan-satpam/update/{id}', [App\Http\Controllers\LogPotonganSatpamController::class, 'update']);
        Route::match(['get', 'post', 'delete'], '/log/potongan-satpam/delete/{id}', [App\Http\Controllers\LogPotonganSatpamController::class, 'destroy']);
        Route::get('/log/potongan-satpam/satpam-options', [App\Http\Controllers\LogPotonganSatpamController::class, 'searchSatpam']);

        // Log Potongan Umana
        Route::get('/log/potongan-umana', [App\Http\Controllers\LogPotonganUmanaController::class, 'index']);
        Route::post('/log/potongan-umana/data', [App\Http\Controllers\LogPotonganUmanaController::class, 'data']);
        Route::get('/log/potongan-umana/show/{id}', [App\Http\Controllers\LogPotonganUmanaController::class, 'show']);
        Route::post('/log/potongan-umana/store', [App\Http\Controllers\LogPotonganUmanaController::class, 'store']);
        Route::post('/log/potongan-umana/update/{id}', [App\Http\Controllers\LogPotonganUmanaController::class, 'update']);
        Route::match(['get', 'post', 'delete'], '/log/potongan-umana/delete/{id}', [App\Http\Controllers\LogPotonganUmanaController::class, 'destroy']);
    });
});
