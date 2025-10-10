<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\GuestReservationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomViewerController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ------------------------------
// PUBLIC ROUTE
// ------------------------------
Route::get('/', function () {
    $isAdmin = auth()->check() && auth()->user()->hasRole('admin');
    return view('welcome', compact('isAdmin'));
})->name('welcome');


// ------------------------------
// AUTH + VERIFIED ROUTES (Guests & Admin)
// ------------------------------
Route::middleware('auth')->group(function () {

    // Redirect dashboard by role
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('guest.reservations.index');
    })->name('dashboard');


    // ----------- Guest Routes -----------
    Route::prefix('my-reservations')->group(function () {
        Route::get('/', [GuestReservationController::class, 'index'])->name('guest.reservations.index')->middleware('verified');
        Route::get('/create', [GuestReservationController::class, 'create'])->name('reservations.create-guest')->middleware('verified');
        Route::post('/', [GuestReservationController::class, 'store'])->name('reservations.store-guest')->middleware('verified');
        Route::patch('/{reservation}/cancel', [GuestReservationController::class, 'cancel'])->name('guest.reservations.cancel')->middleware('verified');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('reservations.my-show')->middleware('verified');
        Route::get('/{reservation}/invoice', [ReservationController::class, 'invoice'])->name('reservations.invoice-guest')->middleware('verified');
    });

    // Room viewing (available rooms)
    Route::get('/available-rooms', [RoomViewerController::class, 'available'])->name('rooms.available');

    // Guest profile update (phone, passport, etc.)
    Route::get('/customer/profile/edit', [CustomerController::class, 'editGuestProfile'])->name('customer.edit-guest-profile');
    Route::put('/customer/profile', [CustomerController::class, 'updateGuestProfile'])->name('customer.update-guest-profile');

    // Breeze Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mark all notifications as read
    Route::get('/notifications/read-all', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.readAll');
});


// ------------------------------
// ADMIN ROUTES (Protected by role:admin)
// ------------------------------
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // CRUD routes
        Route::resource('rooms', RoomController::class)->middleware('verified');
        Route::resource('customers', CustomerController::class)->middleware('verified');
        Route::resource('reservations', ReservationController::class)->middleware('verified');
        Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'create', 'store'])->middleware('verified');

        // Admin invoices
        Route::get('reservations/{id}/invoice-pdf', [ReservationController::class, 'printInvoice'])->name('reservations.invoice.pdf')->middleware('verified');
        Route::get('/reservations/{reservation}/invoice', [ReservationController::class, 'invoice'])->name('reservations.invoice')->middleware('verified');
    });


// ------------------------------
// SOCIALITE ROUTES (GitHub Login)
// ------------------------------
Route::prefix('login/github')->group(function () {
    Route::get('/', [SocialiteController::class, 'redirectToProvider'])->name('login.github');
    Route::get('callback', [SocialiteController::class, 'handleProviderCallback']);
});


// ------------------------------
// LOGOUT ROUTE
// ------------------------------
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


// ------------------------------
// AUTH ROUTES (Breeze default)
// ------------------------------
require __DIR__ . '/auth.php';
