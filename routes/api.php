<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
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

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum')->name('refresh');
Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('verify-email', [AuthController::class, 'verifyEmail'])->name('verify-email');
Route::post('resend-verification', [AuthController::class, 'resendVerification'])->middleware('auth:sanctum')->name('resend-verification');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('/patients', App\Http\Controllers\Api\PatientController::class);
Route::apiResource('/doctors', App\Http\Controllers\Api\DoctorController::class);
Route::apiResource('/wallets', App\Http\Controllers\Api\WalletController::class);
Route::apiResource('/settingses', App\Http\Controllers\Api\SettingsController::class);
Route::apiResource('/users', App\Http\Controllers\Api\UserController::class);
Route::apiResource('/payments', App\Http\Controllers\Api\PaymentController::class);
Route::apiResource('/reviews', App\Http\Controllers\Api\ReviewController::class);
Route::apiResource('/lab-reports', App\Http\Controllers\Api\LabReportController::class);
Route::apiResource('/diagnostics', App\Http\Controllers\Api\DiagnosticController::class);

// Appointment Routes
Route::get('/doctors/{id}/availability', [App\Http\Controllers\Api\AppointmentController::class, 'availability']);
Route::apiResource('/appointments', App\Http\Controllers\Api\AppointmentController::class)->middleware('auth:sanctum');

// Dashboard Routes
Route::prefix('dashboard')->group(function () {
    Route::get('/summary', [App\Http\Controllers\Api\DashboardController::class, 'summary']);
    Route::get('/analytics', [App\Http\Controllers\Api\DashboardController::class, 'analytics']);
    Route::get('/doctor-stats', [App\Http\Controllers\Api\DashboardController::class, 'doctorStats']);
    Route::get('/charts', [App\Http\Controllers\Api\DashboardController::class, 'charts']);
})->middleware('auth:sanctum');

Route::get('/doc', function () {
    return response()->json(json_decode(file_get_contents(storage_path('api-docs/api-docs.json')), JSON_PRETTY_PRINT));
});