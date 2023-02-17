<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::prefix('webhooks')->name('webhooks.')->group(function (): void {
    Route::prefix('packages')->name('packages.')->group(function (): void {
        Route::post('/add', [WebhookController::class, 'add'])
            ->name('add')
            ->withoutMiddleware([VerifyCsrfToken::class]);
    });
});

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('/reset-password/{token}', function ($token) {
    return view('welcome');
})->name('password.reset');

Route::get('/', function () {
    return view('welcome');
});
