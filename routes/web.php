<?php

use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('home.index');
});

Route::get('/booking', function () {
    return view('booking.index');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

use App\Http\Middleware\IsAdmin;
use App\Models\BilliardTable;

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // Admin Routes
    Route::middleware(IsAdmin::class)->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            $tables = BilliardTable::all();
            return view('admin.dashboard', compact('tables'));
        })->name('admin.dashboard');
    });
});
