<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PoliklinikController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\JadwalpoliklinikController;
use App\Http\Controllers\Auth\LoginController;
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


//DASHBOARD
Route::get('/dashboard-admin', [AdminController::class, 'index'])->name('dashboard-admin');
Route::get('/dashboard-pasien', [PasienController::class, 'index'])->name('dashboard-pasien');
Route::get('/dashboard-petugas', [PetugasController::class, 'index'])->name('dashboard-petugas');

// Poliklinik
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
Route::get('/jadwalpoliklinik', [JadwalpoliklinikController::class, 'index'])->name('jadwalpoliklinik.index');
Route::get('/jadwalpoliklinik/create', [JadwalPoliklinikController::class, 'create'])->name('jadwalpoliklinik.create');
Route::post('/jadwalpoliklinik/add', [JadwalpoliklinikController::class, 'add'])->name('jadwalpoliklinik.add');
Route::get('/jadwalpoliklinik/{id}/edit', [JadwalpoliklinikController::class, 'edit'])->name('jadwalpoliklinik.edit');
Route::put('/jadwalpoliklinik/update/{id}', [JadwalpoliklinikController::class, 'update'])->name('jadwalpoliklinik.update');
Route::delete('/jadwalpoliklinik/{id}', [JadwalpoliklinikController::class, 'destroy'])->name('jadwalpoliklinik.destroy');

// Route untuk login
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

Route::middleware(['redirect.if.authenticated'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    // Tambahkan rute lain yang ingin Anda lindungi


// Route untuk register
Route::get('register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('register', [LoginController::class, 'register']);
});

//logout
Route::post('logout', [LoginController::class, 'logout'])->name('logout');