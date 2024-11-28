<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DahboardController;

use App\Http\Controllers\PostController;
use App\Http\Controllers\RestPasswordController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'posts');

// Posts Routes
Route::resource('posts', PostController::class);

// User Posts Route
Route::get('/{user}/posts', [DahboardController::class, 'userPosts'])->name('posts.user');

// Routes for authenticated users
Route::middleware('auth')->group(function () {
    // User Dashboard Route
    Route::get('/dashboard', [DahboardController::class, 'index'])->middleware('verified')->name('dashboard');

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email Verification Notice route
    Route::get('/email/verify', [AuthController::class, 'verifyEmailNotice'])->name('verification.notice');

    // Email Verification Handler route
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmailHandler'])->middleware('signed')->name('verification.verify');

    // Resending the Verification Email route
    Route::post('/email/verification-notification', [AuthController::class, 'verifyEmailResend'])->middleware('throttle:6,1')->name('verification.send');
});

// Routes for guest users
Route::middleware('guest')->group(function () {
    // Register Routes
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Login Routes
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Reset Password Routes
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::post('/forgot-password', [RestPasswordController::class, 'passwordEmail']);
    Route::get('/reset-password/{token}', [RestPasswordController::class, 'passwordReset'])->name('password.reset');
    Route::post('/reset-password', [RestPasswordController::class, 'passwordUpdate'])->name('password.update');
});