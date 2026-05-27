<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\EntryController;
use App\Http\Controllers\Api\FuelController;
use App\Http\Controllers\Api\ReceiptController;
use App\Http\Controllers\Api\SpendingListController;
use App\Http\Controllers\Api\SummaryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API routes — consumed by the Expo mobile app (Sanctum token auth)
|--------------------------------------------------------------------------
*/

// Public auth — no token required.
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Public invitation surface.
Route::get('invitations/{token}', [InvitationController::class, 'show']);
Route::post('invitations/{token}/accept', [InvitationController::class, 'accept']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Account self-service.
    Route::delete('account', [AccountController::class, 'destroy']);

    // Household members & invitations.
    Route::get('account/members', [InvitationController::class, 'index']);
    Route::post('account/invitations', [InvitationController::class, 'store']);
    Route::delete('account/invitations/{invitation}', [InvitationController::class, 'destroy']);
    Route::delete('account/members/{user}', [InvitationController::class, 'removeMember']);

    // Spending lists (people / home / car)
    Route::get('lists', [SpendingListController::class, 'index']);
    Route::post('lists', [SpendingListController::class, 'store']);
    Route::put('lists/{list}', [SpendingListController::class, 'update']);
    Route::delete('lists/{list}', [SpendingListController::class, 'destroy']);

    // Categories
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('categories', [CategoryController::class, 'store']);

    // Entries (individual purchases)
    Route::get('entries', [EntryController::class, 'index']);
    Route::post('entries', [EntryController::class, 'store']);
    Route::get('entries/{entry}', [EntryController::class, 'show']);
    Route::put('entries/{entry}', [EntryController::class, 'update']);
    Route::delete('entries/{entry}', [EntryController::class, 'destroy']);

    // Monthly summary / dashboard
    Route::get('summary', [SummaryController::class, 'index']);

    // Fuel records + stats for the Car list
    Route::get('fuel', [FuelController::class, 'index']);

    // Receipt / bill scanner
    Route::post('receipts/scan', [ReceiptController::class, 'scan']);
    Route::get('receipts', [ReceiptController::class, 'index']);
    Route::get('receipts/{receipt}', [ReceiptController::class, 'show']);
    Route::post('receipts/{receipt}/confirm', [ReceiptController::class, 'confirm']);
});
