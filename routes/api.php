<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryModulController;
use App\Http\Controllers\ModulController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\QuizController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-register-otp', [AuthController::class, 'resendRegisterOtp']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return response()->json(['message' => 'API works!']);
});

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-forgot-otp', [AuthController::class, 'verifyForgotOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// GET (index, show) untuk semua user login (role 1 & 2)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/category_modul', [CategoryModulController::class, 'index']);
    Route::get('/category_modul/{id}', [CategoryModulController::class, 'show']);
    Route::get('/modul', [ModulController::class, 'index']);
    Route::get('/modul/{id}', [ModulController::class, 'show']);
    Route::get('/statistik', [AuthController::class, 'statistik']);

    Route::post('/chat/start', [ChatbotController::class, 'startSession']);
    Route::post('/chat/send', [ChatbotController::class, 'sendMessage']);
    Route::get('/chat/history/{sessionId}', [ChatbotController::class, 'getSessionLogs']);
    Route::post('/chat/end/{sessionId}', [ChatbotController::class, 'endSession']);
    Route::get('/chat/sessions', [ChatbotController::class, 'getAllSessions']);

    // AkunController
    Route::get('/akun/user-list', [AkunController::class, 'listUser']);
    Route::delete('/akun/user/{id}', [AkunController::class, 'deleteUser']);

    // POST, PUT, DELETE hanya untuk role_id=1 (admin)
    Route::middleware('role:1')->group(function () {
        Route::post('/category_modul', [CategoryModulController::class, 'store']);
        Route::put('/category_modul/{id}', [CategoryModulController::class, 'update']);
        Route::delete('/category_modul/{id}', [CategoryModulController::class, 'destroy']);

        Route::post('/modul', [ModulController::class, 'store']);
        Route::post('/modul/{id}', [ModulController::class, 'update']);
        Route::delete('/modul/{id}', [ModulController::class, 'destroy']);
    });
});

// Semua endpoint quiz hanya untuk user login
Route::middleware('auth:sanctum')->group(function () {
    // USER
    Route::get('/quiz', [QuizController::class, 'listQuiz']);
    Route::get('/quiz/{quiz_id}/questions', [QuizController::class, 'getQuestions']);
    Route::post('/quiz/{quiz_id}/submit', [QuizController::class, 'submitAnswers']);

    // ADMIN (role:1)
    Route::middleware('role:1')->group(function () {
        Route::post('/quiz', [QuizController::class, 'store']);
        Route::post('/quiz/{quiz_id}/questions', [QuizController::class, 'addQuestion']);
        Route::post('/quiz/questions/{question_id}/options', [QuizController::class, 'addOption']);
    });
});
