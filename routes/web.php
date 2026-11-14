<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceProyekController;
use App\Http\Controllers\KlienController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ModalProyekController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PembayaranProyekController;
use App\Http\Controllers\ProfilFreelancerController;
use App\Http\Controllers\ProyekController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('proyek', ProyekController::class);
    Route::post('/proyek/{proyek}/modal', [ModalProyekController::class, 'store']);
    Route::delete('/modal/{modalProyek}', [ModalProyekController::class, 'destroy']);
    Route::get('/modal/{modalProyek}/bukti', [ModalProyekController::class, 'download']);
    Route::post('/proyek/{proyek}/pembayaran', [PembayaranProyekController::class, 'store']);
    Route::delete('/pembayaran/{pembayaranProyek}', [PembayaranProyekController::class, 'destroy']);
    Route::get('/pembayaran/{pembayaranProyek}/bukti', [PembayaranProyekController::class, 'download']);
    Route::post('/proyek/{proyek}/invoice/generate', [InvoiceProyekController::class, 'generateOtomatis']);
    Route::post('/proyek/{proyek}/invoice/upload', [InvoiceProyekController::class, 'uploadPlatform']);
    Route::delete('/invoice/{invoice}', [InvoiceProyekController::class, 'destroy']);
    Route::get('/invoice/{invoice}/download', [InvoiceProyekController::class, 'downloadPdf']);
    Route::get('/invoice/{invoice}/file', [InvoiceProyekController::class, 'downloadPlatform']);
    Route::get('/proyek/{proyek}/kontrak-pdf', [PdfController::class, 'cetakKontrak']);
    Route::get('/pengaturan/profil', [ProfilFreelancerController::class, 'edit']);
    Route::put('/pengaturan/profil', [ProfilFreelancerController::class, 'update']);
    Route::resource('klien', KlienController::class);
    Route::get('/master/sumber-order', [MasterDataController::class, 'sources']);
    Route::post('/master/sumber-order', [MasterDataController::class, 'storeSource']);
    Route::put('/master/sumber-order/{sumberOrder}', [MasterDataController::class, 'updateSource']);
    Route::delete('/master/sumber-order/{sumberOrder}', [MasterDataController::class, 'deleteSource']);
    Route::get('/master/kategori-modal', [MasterDataController::class, 'categories']);
    Route::post('/master/kategori-modal', [MasterDataController::class, 'storeCategory']);
    Route::put('/master/kategori-modal/{masterKategoriModal}', [MasterDataController::class, 'updateCategory']);
    Route::delete('/master/kategori-modal/{masterKategoriModal}', [MasterDataController::class, 'deleteCategory']);
    Route::get('/master/status-proyek', [MasterDataController::class, 'statuses']);
    Route::post('/master/status-proyek', [MasterDataController::class, 'storeStatus']);
    Route::put('/master/status-proyek/{masterStatusProyek}', [MasterDataController::class, 'updateStatus']);
    Route::delete('/master/status-proyek/{masterStatusProyek}', [MasterDataController::class, 'deleteStatus']);
});

require __DIR__.'/auth.php';
