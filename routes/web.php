<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'))->middleware('auth');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TicketController::class, 'dashboard'])->name('dashboard');
    Route::resource('tickets', TicketController::class);
    Route::resource('excluded-numbers', \App\Http\Controllers\ExcludedNumberController::class)->except(['create', 'show', 'edit', 'update']);

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('users/import-template', [UserController::class, 'importTemplate'])->name('users.import-template');
        Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    });
});
