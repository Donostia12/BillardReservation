<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingController;
use App\Http\Middleware\IsAdmin;
use App\Models\BilliardTable;

Route::get('/', function () {
    return view('home.index');
});

Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');

Route::middleware('auth')->group(function () {
    Route::get('/booking/{number}/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/booking/{order}/upload', [BookingController::class, 'uploadProof'])->name('booking.upload');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});



// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        $orders = auth()->user()->orders()->with('billiardTable')->latest()->get();
        $notifications = auth()->user()->notifications()->latest()->get();
        return view('dashboard.index', compact('orders', 'notifications'));
    })->name('dashboard');

    // Admin Routes
    Route::middleware(IsAdmin::class)->prefix('admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/orders/{order}/approve', [App\Http\Controllers\AdminController::class, 'approveOrder'])->name('admin.orders.approve');
        Route::post('/orders/{order}/reject', [App\Http\Controllers\AdminController::class, 'rejectOrder'])->name('admin.orders.reject');
    });

    // Admin API Routes
    Route::middleware(IsAdmin::class)->prefix('api/admin')->group(function () {
        Route::get('/pending-orders', [App\Http\Controllers\AdminController::class, 'getPendingOrders']);
    });
});
