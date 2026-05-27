<?php

use App\Http\Controllers\Marketing\SiteController;
use App\Http\Controllers\Web\InvitationController as WebInvitationController;
use App\Http\Controllers\Web\ResetPasswordController;
use Illuminate\Support\Facades\Route;

// Marketing.
Route::get('/',         [SiteController::class, 'home'])->name('home');
Route::get('/pricing',  [SiteController::class, 'pricing'])->name('pricing');
Route::get('/privacy',  [SiteController::class, 'privacy'])->name('privacy');
Route::get('/terms',    [SiteController::class, 'terms'])->name('terms');
Route::get('/sitemap.xml', [SiteController::class, 'sitemap']);

// Auth (web).
Route::get('/reset-password',  [ResetPasswordController::class, 'show'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update');

// Invitation accept (public, opened from the email link).
Route::get('/invitation', [WebInvitationController::class, 'show'])->name('invitation.show');
