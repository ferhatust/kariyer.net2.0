<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IsverenController;
use App\Http\Controllers\KullaniciController;
use App\Http\Controllers\IsIlaniController;
use App\Http\Controllers\BasvuruController;

Route::prefix('isverenler')->group(function () {
    Route::get('/', [IsverenController::class, 'index']);
    Route::post('/', [IsverenController::class, 'store']);
    Route::post('/login', [IsverenController::class, 'login']);
    Route::get('/me', [IsverenController::class, 'me']);
    Route::match(['put','patch'],'/me', [IsverenController::class, 'updateMe']);
    Route::get('{id}', [IsverenController::class, 'show']);
    Route::get('{id}/is_ilanlari', [IsverenController::class, 'isIlanlari']);
    Route::match(['put','patch'],'{id}', [IsverenController::class, 'update']);
    Route::delete('{id}', [IsverenController::class, 'destroy']);
});

Route::prefix('kullanicilar')->group(function () {
    Route::get('/', [KullaniciController::class, 'index']);
    Route::post('/', [KullaniciController::class, 'store']);
    Route::post('/login', [KullaniciController::class, 'login']);
    Route::get('/me', [KullaniciController::class, 'me']);
    Route::match(['put','patch'],'/me', [KullaniciController::class, 'updateMe']);
    Route::get('{id}', [KullaniciController::class, 'show']);
    Route::get('{id}/basvurular', [KullaniciController::class, 'basvurular']);
    Route::match(['put','patch'],'{id}', [KullaniciController::class, 'update']);
    Route::delete('{id}', [KullaniciController::class, 'destroy']);
});

Route::prefix('is_ilanlari')->group(function () {
    Route::get('/', [IsIlaniController::class, 'index']);
    Route::post('/', [IsIlaniController::class, 'store']);
    Route::get('{id}', [IsIlaniController::class, 'show']);
    Route::match(['put','patch'],'{id}', [IsIlaniController::class, 'update']);
    Route::delete('{id}', [IsIlaniController::class, 'destroy']);
});

Route::prefix('basvurular')->group(function () {
    Route::get('/', [BasvuruController::class, 'index']);
    Route::get('/me', [BasvuruController::class, 'myIndex']);
    Route::post('/', [BasvuruController::class, 'store']);
    Route::get('{id}', [BasvuruController::class, 'show']);
    Route::match(['put','patch'],'{id}', [BasvuruController::class, 'update']);
    Route::delete('{id}', [BasvuruController::class, 'destroy']);
});
