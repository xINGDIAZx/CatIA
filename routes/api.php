<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\DataFlowController;

// Rutas públicas
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/catiaConsejo', [AIController::class, 'catiaConsejo'])->name('catiaConsejo');

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/catia', [AIController::class, 'catia'])->name('catia'); // falta ver la seguridad de la ruta
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/dataUser', [DataFlowController::class, 'dataUser'])->name('dataUser');
    Route::post('/updateUser', [DataFlowController::class, 'updateUser'])->name('updateUser');
    Route::post('/getWallets', [DataFlowController::class, 'getWallets'])->name('getWallets');
    Route::post('/addWallet', [DataFlowController::class, 'addWallet'])->name('addWallet');
    Route::post('/deleteWallet', [DataFlowController::class, 'deleteWallet'])->name('deleteWallet');
    Route::post('/refreshUser', [DataFlowController::class, 'refreshUser'])->name('refreshUser');
    Route::post('/getMovementsbyWallet', [DataFlowController::class, 'getMovementsbyWallet'])->name('getMovementsbyWallet');
    Route::post('/addMovementsbyWallet', [DataFlowController::class, 'addMovementsbyWallet'])->name('addMovementsbyWallet');
});

