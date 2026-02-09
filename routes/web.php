<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PenempatanController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\UmpController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\FungsiController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ResikoController;
use App\Http\Controllers\HarianshiftController;
use App\Http\Controllers\KuotajamController;
use App\Http\Controllers\MasakerjaController;
use App\Http\Controllers\PakaianController;
use App\Http\Controllers\PenyesuaianController;

use App\Models\Penempatan;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Auth Routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'index'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'authenticate']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');

// Public Routes (No Auth Required)
Route::get('/verify-tagihan/{token}', [PaketController::class, 'verifyTagihan'])->name('tagihan.verify');
Route::get('/verify-tagihan/{token}/download', [PaketController::class, 'downloadVerifiedPDF'])->name('tagihan.verify.download');

// Public route for QR code validation
Route::get('/validate/{token}', [App\Http\Controllers\ContractValidationController::class, 'showValidation'])->name('contract.validate');
Route::get('/api/validate/{token}', [App\Http\Controllers\ContractValidationController::class, 'validateApi'])->name('contract.validate.api');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/karyawan', [KaryawanController::class, 'index']);
    Route::get('/gettambah-karyawan', [KaryawanController::class, 'getTambah']);
    Route::post('/tambah-karyawan', [KaryawanController::class, 'setTambah']);
    Route::get('/karyawan/sampah', [KaryawanController::class, 'trash']);
    Route::get('/getupdate-karyawan/{id}', [KaryawanController::class, 'getUpdate']);
    Route::post('/update-karyawan/{id}', [KaryawanController::class, 'setUpdate']);
    Route::get('/delete-karyawan/{id}', [KaryawanController::class, 'destroy'])->name('delete-master-data');
    Route::get('/restore-karyawan/{id}', [KaryawanController::class, 'restore']);
    Route::get('/detail-karyawan/{id}', [KaryawanController::class, 'detail']);
    Route::post('/mutasi-paket', [KaryawanController::class, 'simpanMutasi']);
    Route::post('/ganti-shift', [KaryawanController::class, 'simpanShift']);
    Route::post('/promosi-jabatan', [KaryawanController::class, 'simpanPromosi']);
    Route::post('/ganti-area', [KaryawanController::class, 'simpanArea']);
    Route::post('/ganti-pakaian', [KaryawanController::class, 'simpanPakaian']);


    Route::get('/perusahaan', [PerusahaanController::class, 'index']);
    Route::get('/gettambah-perusahaan', [PerusahaanController::class, 'getTambah']);
    Route::post('/tambah-perusahaan', [PerusahaanController::class, 'setTambah']);
    Route::get('/perusahaan/sampah', [PerusahaanController::class, 'trash']);
    Route::get('/getupdate-perusahaan/{id}', [PerusahaanController::class, 'getUpdate']);
    Route::post('/update-perusahaan/{id}', [PerusahaanController::class, 'setUpdate']);
    Route::get('/delete-perusahaan/{id}', [PerusahaanController::class, 'destroy']);
    Route::get('/restore-perusahaan/{id}', [PerusahaanController::class, 'restore']);

    Route::get('/departemen', [DepartemenController::class, 'index']);
    Route::get('/gettambah-departemen', [DepartemenController::class, 'getTambah']);
    Route::post('/tambah-departemen', [DepartemenController::class, 'setTambah']);
    Route::get('/departemen/sampah', [DepartemenController::class, 'trash']);
    Route::get('/getupdate-departemen/{id}', [DepartemenController::class, 'getUpdate']);
    Route::post('/update-departemen/{id}', [DepartemenController::class, 'setUpdate']);
    Route::get('/delete-departemen/{id}', [DepartemenController::class, 'destroy']);
    Route::get('/restore-departemen/{id}', [DepartemenController::class, 'restore']);

    Route::get('/fungsi', [FungsiController::class, 'index']);
    Route::get('/gettambah-fungsi', [FungsiController::class, 'getTambah']);
    Route::post('/tambah-fungsi', [FungsiController::class, 'setTambah']);
    Route::get('/fungsi/sampah', [FungsiController::class, 'trash']);
    Route::get('/getupdate-fungsi/{id}', [FungsiController::class, 'getUpdate']);
    Route::post('/update-fungsi/{id}', [FungsiController::class, 'setUpdate']);
    Route::get('/delete-fungsi/{id}', [FungsiController::class, 'destroy']);
    Route::get('/restore-fungsi/{id}', [FungsiController::class, 'restore']);

    Route::get('/jabatan', [JabatanController::class, 'index']);
    Route::get('/gettambah-jabatan', [JabatanController::class, 'getTambah']);
    Route::post('/tambah-jabatan', [JabatanController::class, 'setTambah']);
    Route::get('/jabatan/sampah', [JabatanController::class, 'trash']);
    Route::get('/getupdate-jabatan/{id}', [JabatanController::class, 'getUpdate']);
    Route::post('/update-jabatan/{id}', [JabatanController::class, 'setUpdate']);
    Route::get('/delete-jabatan/{id}', [JabatanController::class, 'destroy']);
    Route::get('/restore-jabatan/{id}', [JabatanController::class, 'restore']);

    Route::get('/lokasi', [LokasiController::class, 'index']);
    Route::get('/gettambah-lokasi', [LokasiController::class, 'getTambah']);
    Route::post('/tambah-lokasi', [LokasiController::class, 'setTambah']);
    Route::get('/lokasi/sampah', [LokasiController::class, 'trash']);
    Route::get('/getupdate-lokasi/{id}', [LokasiController::class, 'getUpdate']);
    Route::post('/update-lokasi/{id}', [LokasiController::class, 'setUpdate']);
    Route::get('/delete-lokasi/{id}', [LokasiController::class, 'destroy']);
    Route::get('/restore-lokasi/{id}', [LokasiController::class, 'restore']);

    Route::get('/resiko', [ResikoController::class, 'index']);
    Route::get('/gettambah-resiko', [ResikoController::class, 'getTambah']);
    Route::post('/tambah-resiko', [ResikoController::class, 'setTambah']);
    Route::get('/resiko/sampah', [ResikoController::class, 'trash']);
    Route::get('/getupdate-resiko/{id}', [ResikoController::class, 'getUpdate']);
    Route::post('/update-resiko/{id}', [ResikoController::class, 'setUpdate']);
    Route::get('/delete-resiko/{id}', [ResikoController::class, 'destroy']);
    Route::get('/restore-resiko/{id}', [ResikoController::class, 'restore']);

    Route::get('/harianshift', [HarianshiftController::class, 'index']);
    Route::get('/gettambah-harianshift', [HarianshiftController::class, 'getTambah']);
    Route::post('/tambah-harianshift', [HarianshiftController::class, 'setTambah']);
    Route::get('/harianshift/sampah', [HarianshiftController::class, 'trash']);
    Route::get('/getupdate-harianshift/{id}', [HarianshiftController::class, 'getUpdate']);
    Route::post('/update-harianshift/{id}', [HarianshiftController::class, 'setUpdate']);
    Route::get('/delete-harianshift/{id}', [HarianshiftController::class, 'destroy']);
    Route::get('/restore-harianshift/{id}', [HarianshiftController::class, 'restore']);

    Route::get('/kuotajam', [KuotajamController::class, 'index']);
    Route::get('/gettambah-kuotajam', [KuotajamController::class, 'getTambah']);
    Route::post('/tambah-kuotajam', [KuotajamController::class, 'setTambah']);
    Route::get('/kuotajam/sampah', [KuotajamController::class, 'trash']);
    Route::get('/getupdate-kuotajam/{id}', [KuotajamController::class, 'getUpdate']);
    Route::post('/update-kuotajam/{id}', [KuotajamController::class, 'setUpdate']);
    Route::get('/delete-kuotajam/{id}', [KuotajamController::class, 'destroy']);
    Route::get('/restore-kuotajam/{id}', [KuotajamController::class, 'restore']);

    Route::get('/masakerja', [MasakerjaController::class, 'index']);
    Route::get('/gettambah-masakerja', [MasakerjaController::class, 'getTambah']);
    Route::post('/tambah-masakerja', [MasakerjaController::class, 'setTambah']);
    Route::get('/masakerja/sampah', [MasakerjaController::class, 'trash']);
    Route::get('/getupdate-masakerja/{id}', [MasakerjaController::class, 'getUpdate']);
    Route::post('/update-masakerja/{id}', [MasakerjaController::class, 'setUpdate']);
    Route::get('/delete-masakerja/{id}', [MasakerjaController::class, 'destroy']);
    Route::get('/restore-masakerja/{id}', [MasakerjaController::class, 'restore']);

    Route::get('/pakaian', [PakaianController::class, 'index']);
    Route::get('/gettambah-pakaian', [PakaianController::class, 'getTambah']);
    Route::post('/tambah-pakaian', [PakaianController::class, 'setTambah']);
    Route::get('/pakaian/sampah', [PakaianController::class, 'trash']);
    Route::get('/getupdate-pakaian/{id}', [PakaianController::class, 'getUpdate']);
    Route::get('/update-pakaian/{id}', [PakaianController::class, 'setUpdate']);
    Route::get('/delete-pakaian/{id}', [PakaianController::class, 'destroy']);
    Route::get('/restore-pakaian/{id}', [PakaianController::class, 'restore']);

    Route::get('/penyesuaian', [PenyesuaianController::class, 'index']);
    Route::get('/gettambah-penyesuaian', [PenyesuaianController::class, 'getTambah']);
    Route::post('/tambah-penyesuaian', [PenyesuaianController::class, 'setTambah']);
    Route::get('/penyesuaian/sampah', [PenyesuaianController::class, 'trash']);
    Route::get('/getupdate-penyesuaian/{id}', [PenyesuaianController::class, 'getUpdate']);
    Route::post('/update-penyesuaian/{id}', [PenyesuaianController::class, 'setUpdate']);
    Route::get('/delete-penyesuaian/{id}', [PenyesuaianController::class, 'destroy']);
    Route::get('/restore-penyesuaian/{id}', [PenyesuaianController::class, 'restore']);

    Route::get('/medical-checkup', [App\Http\Controllers\MedicalCheckupController::class, 'index'])->name('medical-checkup');
    Route::post('/tambah-medical-checkup', [App\Http\Controllers\MedicalCheckupController::class, 'setTambah'])->name('medical-checkup.store');
    Route::get('/getupdate-medical-checkup/{id}', [App\Http\Controllers\MedicalCheckupController::class, 'getUpdate']);
    Route::post('/update-medical-checkup/{id}', [App\Http\Controllers\MedicalCheckupController::class, 'setUpdate'])->name('medical-checkup.update');
    Route::get('/delete-medical-checkup/{id}', [App\Http\Controllers\MedicalCheckupController::class, 'destroy'])->name('medical-checkup.destroy');
    Route::get('/medical-checkup/sampah', [App\Http\Controllers\MedicalCheckupController::class, 'trash'])->name('medical-checkup.trash');
    Route::get('/restore-medical-checkup/{id}', [App\Http\Controllers\MedicalCheckupController::class, 'restore'])->name('medical-checkup.restore');

    Route::get('/unit-kerja', [UnitKerjaController::class, 'index']);
    Route::get('/gettambah-unit', [UnitKerjaController::class, 'getTambah']);
    Route::post('/tambah-unit', [UnitKerjaController::class, 'setTambah']);
    Route::get('/unit-kerja/sampah', [UnitKerjaController::class, 'trash']);
    Route::get('/getupdate-unit/{id}', [UnitKerjaController::class, 'getUpdate']);
    Route::post('/update-unit/{id}', [UnitKerjaController::class, 'setUpdate']);
    Route::get('/delete-unit/{id}', [UnitKerjaController::class, 'destroy']);
    Route::get('/restore-unit/{id}', [UnitKerjaController::class, 'restore']);

    Route::get('/gettambah-bidang', [UnitKerjaController::class, 'getTambahBidang']);
    Route::post('/tambah-bidang', [UnitKerjaController::class, 'setTambahBidang']);
    Route::get('/gettambah-area', [UnitKerjaController::class, 'getTambahArea']);
    Route::post('/tambah-area', [UnitKerjaController::class, 'setTambahArea']);
    Route::get('/get-bidang/{unit_id}', [UnitKerjaController::class, 'getBidang']);

    Route::get('/penempatan', [PenempatanController::class, 'index']);
    Route::get('/gettambah-penempatan', [PenempatanController::class, 'getTambah']);
    Route::post('/tambah-penempatan', [PenempatanController::class, 'setTambah']);
    Route::post('/tambah-penempatan2', [PenempatanController::class, 'setTambah2']);
    Route::get('/getupdate-unit-kerja/{id}', [PenempatanController::class, 'getUpdate']);
    Route::post('/update-unit-kerja/{id}', [PenempatanController::class, 'setUpdate']);
    Route::get('/get-bidang/{unit_id}', [PenempatanController::class, 'getBidang']);
    Route::get('/get-area/{bidang_id}', [PenempatanController::class, 'getArea']);

    Route::post('/set-berhenti', [PenempatanController::class, 'setBerhenti']);
    Route::get('/tambah-pengganti/{id}', [PenempatanController::class, 'formPengganti']);
    Route::post('/simpan-pengganti/{id}', [PenempatanController::class, 'simpanPengganti']);
    Route::post('/ganti-karyawan/{id}', [PenempatanController::class, 'gantiKaryawan']);
    Route::get('/get-mpp-history/{id}', [PenempatanController::class, 'getHistory']);

    // API Routes untuk dropdown data
    Route::get('/api/jabatan', function () {
        return DB::table('md_jabatan')->select('kode_jabatan', 'jabatan')->get();
    });
    Route::get('/api/lokasi', function () {
        return DB::table('md_lokasi')->select('kode_lokasi', 'lokasi')->get();
    });
    Route::get('/api/paket', function () {
        return DB::table('md_paket')->select('paket_id', 'paket')->get();
    });

    Route::post('/import-karyawan', [ImportController::class, 'import']);
    Route::post('/import-mutasi', [ImportController::class, 'importMutasi']);
    Route::post('/import-pakaian', [ImportController::class, 'importPakaian']);
    Route::post('/import-template-baru', [ImportController::class, 'importTemplateBaru']);

    // Dynamic Template Downloads
    Route::get('/templates/mutasi-promosi', [App\Http\Controllers\TemplateController::class, 'downloadMutasi'])->name('template.mutasi');
    Route::get('/templates/pakaian', [App\Http\Controllers\TemplateController::class, 'downloadPakaian'])->name('template.pakaian');
    Route::get('/templates/perusahaan', [App\Http\Controllers\TemplateController::class, 'downloadPerusahaan'])->name('template.perusahaan');
    Route::get('/templates/karyawan', [App\Http\Controllers\TemplateController::class, 'downloadKaryawan'])->name('template.karyawan');

    Route::get('/paket', [PaketController::class, 'index']);
    Route::get('/paket/sampah', [PaketController::class, 'trash']);
    Route::get('/paket/{id}', [PaketController::class, 'show']);
    Route::get('/datapaket', [PaketController::class, 'indexpaket']);
    Route::get('/gettambah-paket', [PaketController::class, 'getTambah']);
    Route::post('/tambah-paket', [PaketController::class, 'setTambah']);
    Route::post('/tambah-karyawan-paket', [PaketController::class, 'storeKaryawan']); // New Route
    Route::get('/getupdate-paket/{id}', [PaketController::class, 'getUpdate']);
    Route::post('/update-paket/{id}', [PaketController::class, 'setUpdate']);
    Route::get('/delete-paket/{id}', [PaketController::class, 'destroy']);
    Route::get('/restore-paket/{id}', [PaketController::class, 'restore']);

    Route::get('/ump', [UmpController::class, 'index']);
    Route::get('/gettambah-ump-tahunan', [UmpController::class, 'getTambah']);
    Route::post('/tambah-ump-tahunan', [UmpController::class, 'setTambah']);
    Route::get('/gettambah-ump', [UmpController::class, 'getTambah2']);
    Route::post('/tambah-ump', [UmpController::class, 'setTambah2']);
    Route::get('/ump/sampah', [UmpController::class, 'trash']);
    Route::get('/getupdate-ump/{id}', [UmpController::class, 'getUpdate']);
    Route::post('/update-ump/{id}', [UmpController::class, 'setUpdate']);
    Route::get('/delete-ump/{id}', [UmpController::class, 'destroy']);
    Route::get('/restore-ump/{id}', [UmpController::class, 'restore']);

    Route::get('/kota/{provinsi_id}', [WilayahController::class, 'getKota']);

    // Routes untuk Tagihan
    Route::get('/paket/{id}/tagihan', [PaketController::class, 'lihatTagihan'])->name('paket.tagihan');
    Route::get('/paket/{id}/pdf', [PaketController::class, 'generatePDF'])->name('paket.pdf.download');

    // Routes untuk Kalkulator Kontrak
    Route::get('/kalkulator-kontrak', [App\Http\Controllers\NilaiKontrakController::class, 'index'])->name('kalkulator.index');
    Route::post('/kalkulator-kontrak/calculate', [App\Http\Controllers\NilaiKontrakController::class, 'calculate'])->name('kalkulator.calculate');
    Route::get('/kalkulator-kontrak/show', [App\Http\Controllers\NilaiKontrakController::class, 'show'])->name('kalkulator.show');
    Route::post('/kalkulator-kontrak/recalculate-all', [App\Http\Controllers\NilaiKontrakController::class, 'recalculateAll'])->name('kalkulator.recalculate');
    Route::get('/kalkulator-kontrak/history/{paket_id}', [App\Http\Controllers\NilaiKontrakController::class, 'history'])->name('kalkulator.history');
    Route::get('/kalkulator-kontrak/cetak-thr/{paket_id}', [App\Http\Controllers\NilaiKontrakController::class, 'cetakThr'])->name('kalkulator.cetak-thr');
    Route::post('/kalkulator-kontrak/export', [App\Http\Controllers\NilaiKontrakController::class, 'export'])->name('kalkulator.export');

    // API untuk AJAX
    Route::get('/api/nilai-kontrak/calculate/{paket_id}', [App\Http\Controllers\NilaiKontrakController::class, 'apiCalculate'])->name('api.kalkulator.calculate');

});
