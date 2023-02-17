<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\VersionController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Health Routes
Route::prefix('health')->name('health.')->group(function (): void {
    Route::get('ping', [HealthController::class, 'ping'])->name('ping');
    Route::get('info', [HealthController::class, 'info'])->name('info');
});

Route::prefix('v1')->group(function (): void {
    Route::middleware('throttle:100,1')->group(function (): void {
        // Auth Routes
        Route::prefix('auth')->middleware('throttle:30,1')->name('auth.')->group(function (): void {
            Route::post('/login', [AuthController::class, 'login'])->name('login');
            Route::post('/register', [AuthController::class, 'create'])->name('register');
            Route::post('/resendVerification', [AuthController::class, 'resendVerification'])->name('resend.verification');
            Route::post('/reset/email', [AuthController::class, 'forgottenPassword'])->name('reset.email');
            Route::post('/reset/password', [AuthController::class, 'resetForgottenPassword'])->name('reset.password');
            Route::get('/verify/email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('email.verification');

            /** AUTH - auth:sanctum - Token required */
            Route::group(['middleware' => ['auth:sanctum']], function (): void {
                Route::get('/me', [AuthController::class, 'me'])->name('me');
                Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
                Route::get('/token/check', [AuthController::class, 'checkToken'])->name('check');
            });
        });

        // Other Routes
        //Route::get('/', [IndexController::class, 'index'])->name('api.index');

        /** AUTH - auth:sanctum - Token required */
        Route::group(['middleware' => ['auth:sanctum']], function (): void {
            // Other protected routes go here
        });

        // Non protected Routes
        Route::prefix('webhooks')->name('webhooks.')->group(function (): void {
            Route::prefix('packages')->name('packages.')->group(function (): void {
                Route::get('status/{package}', [WebhookController::class, 'status'])->name('status');
                Route::get('log', [WebhookController::class, 'log'])->name('logs');
            });
        });
    });

    Route::prefix('section/{type}')->middleware(['gzip'])->name('section.')->group(function (): void {
        Route::get('/', [VersionController::class, 'index'])->name('versions.index');
        Route::prefix('{version}')->group(function (): void {
            Route::get('/', [VersionController::class, 'show'])->name('versions.show');
            Route::name('content.')->group(function (): void {
                Route::get('toc', [ContentController::class, 'toc'])->name('toc');
            });
        });
    });
});
