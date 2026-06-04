<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SlotController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/slots', [SlotController::class, 'index'])->name('slots.index');

Route::middleware('auth')->group(function () {
    Route::post('/slots/{id}', [SlotController::class, 'book'])->name('slots.book');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', IsAdmin::class])->group(function () {
    Route::post('/slots', [SlotController::class, 'store'])->name('slots.store');
    Route::put('/slots/{id}', [SlotController::class, 'update'])->name('slots.update');
});

Route::get('/', function () {
    return redirect()->route('slots.index');
});

require __DIR__.'/auth.php';
