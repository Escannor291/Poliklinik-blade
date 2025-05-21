<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PoliklinikController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\JadwalpoliklinikController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DatauserController;
use App\Http\Controllers\DatapasienController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\LaporanPendaftaranController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

//DASHBOARD - Add middleware to protect dashboard routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard-admin', [AdminController::class, 'index'])->name('dashboard-admin')->middleware('role:admin');
    Route::get('/dashboard-pasien', [PasienController::class, 'index'])->name('dashboard-pasien')->middleware('role:pasien');
    Route::get('/dashboard-petugas', [PetugasController::class, 'index'])->name('dashboard-petugas')->middleware('role:petugas');
});

// Poliklinik - only admin/petugas should access these
Route::middleware(['auth', 'role:admin,petugas'])->group(function () {
    Route::get('/poliklinik', [PoliklinikController::class, 'index'])->name('poliklinik.index');
    Route::get('/poliklinik/create', [PoliklinikController::class, 'create'])->name('poliklinik.create');
    Route::post('/poliklinik/add', [PoliklinikController::class, 'add'])->name('poliklinik.add');
    Route::get('/poliklinik/edit/{id}', [PoliklinikController::class, 'edit'])->name('poliklinik.edit');
    Route::post('/poliklinik/update/{id}', [PoliklinikController::class, 'update'])->name('poliklinik.update');
    Route::delete('/poliklinik/{id}', [PoliklinikController::class, 'destroy'])->name('poliklinik.destroy');

    //Dokter
    Route::get('/dokter', [DokterController::class, 'index'])->name('dokter.index');
    Route::get('/dokter/create', [DokterController::class, 'create'])->name('dokter.create');
    Route::post('/dokter/add', [DokterController::class, 'add'])->name('dokter.add');
    Route::delete('/dokter/{id}', [DokterController::class, 'destroy'])->name('dokter.destroy');
    Route::get('/dokter/edit/{id}', [DokterController::class, 'edit'])->name('dokter.edit');
    Route::put('/dokter/update/{id}', [DokterController::class, 'update'])->name('dokter.update');
    Route::get('/dokter/{id}', [DokterController::class, 'show'])->name('dokter.show');

    //JADWAL POLIKLINIK
    Route::get('/jadwalpoliklinik', [JadwalPoliklinikController::class, 'index'])->name('jadwalpoliklinik.index');
    Route::get('/jadwalpoliklinik/create', [JadwalPoliklinikController::class, 'create'])->name('jadwalpoliklinik.create');
    Route::post('/jadwalpoliklinik/add', [JadwalPoliklinikController::class, 'add'])->name('jadwalpoliklinik.add');
    Route::get('/jadwalpoliklinik/{id}/edit', [JadwalPoliklinikController::class, 'edit'])->name('jadwalpoliklinik.edit');
    Route::put('/jadwalpoliklinik/update/{id}', [JadwalPoliklinikController::class, 'update'])->name('jadwalpoliklinik.update');
    Route::delete('/jadwalpoliklinik/{id}', [JadwalPoliklinikController::class, 'destroy'])->name('jadwalpoliklinik.destroy');

    //Data User - admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/user', [DatauserController::class, 'index'])->name('user.index');
        Route::get('/user/create', [DatauserController::class, 'create'])->name('user.create');
        Route::post('/user/add', [DatauserController::class, 'add'])->name('user.add');
        Route::get('/user/{id}/edit', [DatauserController::class, 'edit'])->name('user.edit');
        Route::put('/user/{id}', [DatauserController::class, 'update'])->name('user.update');
        Route::delete('/user/{id}', [DatauserController::class, 'destroy'])->name('user.destroy');
    });

    // Admin registration routes
    Route::get('/admin-pendaftaran', [PendaftaranController::class, 'adminRegistrationForm'])->name('pendaftaran.admin-form');
    Route::post('/admin-pendaftaran/store', [PendaftaranController::class, 'storeAdminRegistration'])->name('pendaftaran.store-admin');
});

// Route untuk login & registration
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [LoginController::class, 'login'])->middleware('guest');

// Route untuk register
Route::get('register', [LoginController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('register', [LoginController::class, 'register'])->middleware('guest');

// Logout route
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Profile Routes - accessible by any authenticated user
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('user.profile');
    Route::put('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/user/detail', [App\Http\Controllers\UserController::class, 'detail'])->name('user.detail');
});

//Pasien
Route::middleware('auth')->group(function () {
    Route::get('/datapribadi/{id}', [DatapasienController::class, 'show'])->name('pasien.show');
    Route::get('/datapribadi/{id}/edit', [DatapasienController::class, 'edit'])->name('pasien.edit');
    Route::put('/datapribadi/{id}', [DatapasienController::class, 'update'])->name('pasien.update');
    Route::post('/datapribadi/{id}/update-insurance', [DatapasienController::class, 'updateInsurance'])->name('pasien.update-insurance');
});

Route::get('/datapasien', [DatapasienController::class, 'index'])->name('pasien.index');
Route::delete('/datapasien/{id}', [DatapasienController::class, 'destroy'])->name('pasien.destroy');
// Add these new routes for creating patients
Route::get('/datapasien/create', [DatapasienController::class, 'create'])->name('pasien.create');
Route::post('/datapasien/add', [DatapasienController::class, 'store'])->name('pasien.store');

// Add the Pendaftaran routes - accessible by authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/pendaftaran-pasien', [PendaftaranController::class, 'pasienDashboard'])->name('pendaftaran.pasien');
    Route::get('/pendaftaran/with-layout', [PendaftaranController::class, 'indexWithLayout'])->name('pendaftaran.index.with-layout');
    Route::get('/pendaftaran/get-jadwal', [PendaftaranController::class, 'getJadwal'])->name('pendaftaran.get-jadwal');
    Route::post('/pendaftaran/store', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
    Route::get('/pendaftaran/{jadwal_id}', [PendaftaranController::class, 'showForm'])->name('pendaftaran.show');
    Route::get('/riwayat-pendaftaran', [PendaftaranController::class, 'history'])->name('pendaftaran.history');

    // Add new admin registration routes
    Route::get('/admin-pendaftaran', [PendaftaranController::class, 'adminRegistrationForm'])->name('pendaftaran.admin-form')
        ->middleware('role:admin,petugas');
    Route::post('/admin-pendaftaran/store', [PendaftaranController::class, 'storeAdminRegistration'])->name('pendaftaran.store-admin')
        ->middleware('role:admin,petugas');

    // Laporan Pendaftaran routes
    Route::get('/laporan-pendaftaran', [LaporanPendaftaranController::class, 'index'])->name('laporan_pendaftaran.index');
    Route::get('/laporan-pendaftaran/export-pdf', [LaporanPendaftaranController::class, 'exportPdf'])->name('laporan_pendaftaran.export_pdf');
    Route::get('/laporan-pendaftaran/get-dokters', [LaporanPendaftaranController::class, 'getDoktersByPoliklinik'])->name('laporan_pendaftaran.get_dokters');
    Route::get('/laporan-pendaftaran/{id}/edit', [LaporanPendaftaranController::class, 'edit'])->name('laporan_pendaftaran.edit')->middleware('role:admin');
    Route::put('/laporan-pendaftaran/{id}', [LaporanPendaftaranController::class, 'update'])->name('laporan_pendaftaran.update')->middleware('role:admin');
    Route::delete('/laporan-pendaftaran/{id}', [LaporanPendaftaranController::class, 'destroy'])->name('laporan_pendaftaran.destroy')->middleware('role:admin');
    Route::get('/laporan-pendaftaran/{id}/pdf', [LaporanPendaftaranController::class, 'downloadPdf'])->name('laporan_pendaftaran.download_pdf');
});