<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EntryController;
use App\Http\Controllers\Api\ReceiptController;
use App\Http\Controllers\Api\SmsController;
use App\Http\Controllers\Api\SpendingListController;
use App\Http\Controllers\Api\SummaryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API routes — consumed by the Expo mobile app (Sanctum token auth)
|--------------------------------------------------------------------------
*/

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

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

    // Receipt / bill scanner
    Route::post('receipts/scan', [ReceiptController::class, 'scan']);
    Route::get('receipts', [ReceiptController::class, 'index']);
    Route::get('receipts/{receipt}', [ReceiptController::class, 'show']);
    Route::post('receipts/{receipt}/confirm', [ReceiptController::class, 'confirm']);

    // Transaction SMS importer
    Route::post('sms/import', [SmsController::class, 'import']);
    Route::get('sms', [SmsController::class, 'index']);
});
