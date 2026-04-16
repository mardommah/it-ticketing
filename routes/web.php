<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TicketController::class, 'dashboard'])->name('dashboard');
    Route::resource('tickets', TicketController::class);
});
