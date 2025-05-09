<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EncryptionController;

Route::get('/', [EncryptionController::class, 'index'])->name('encryption.index');
Route::post('/encrypt', [EncryptionController::class, 'encrypt'])->name('encryption.encrypt');
Route::post('/decrypt', [EncryptionController::class, 'decrypt'])->name('encryption.decrypt');
