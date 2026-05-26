<?php

use App\Http\Controllers\Web\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::get('/reset-password', [ResetPasswordController::class, 'show'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update');
