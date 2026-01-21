<?php

use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/sms', [ProfileController::class, 'updateSms'])->name('profile.update.sms');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', [PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::post('/', [PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::put('/{prescription}', [PrescriptionController::class, 'update'])->name('prescriptions.update');
    Route::delete('/{prescription}', [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
    Route::put('/{prescription}/prepare', [PrescriptionController::class, 'prepare'])->name('prescriptions.prepare');
    Route::put('/{prescription}/deliver', [PrescriptionController::class, 'deliver'])->name('prescriptions.deliver');
    Route::put('/{prescription}/cancel', [PrescriptionController::class, 'cancel'])->name('prescriptions.cancel');
    Route::put('/{prescription}/close', [PrescriptionController::class, 'close'])->name('prescriptions.close');
});

require __DIR__ . '/auth.php';
