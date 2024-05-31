<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::post('stk/callback', [PaymentController::class, 'stkCallback']);
Route::post('/stk/callback', [TransactionController::class, 'callback_url']);
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/mpesa_view', [PaymentController::class, 'mpesa_view'])->name('mpesa_view');
    // Route::post('/stk_push', [PaymentController::class, 'store'])->name('stk_push');
    Route::post('/stk_push', [TransactionController::class, 'stkPushRequest'])->name('stk_push');
});

require __DIR__ . '/auth.php';
