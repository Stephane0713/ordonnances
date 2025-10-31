<?php

use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', [PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::post('/', [PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::put('/{prescription}', [PrescriptionController::class, 'update'])->name('prescriptions.update');
    Route::delete('/{prescription}', [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
});

require __DIR__ . '/auth.php';
