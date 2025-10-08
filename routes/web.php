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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// PUBLIC ROUTE
Route::get('/', function () {
    $isAdmin = auth()->check() && auth()->user()->hasRole('admin');
    return view('welcome', compact('isAdmin'));
})->name('welcome');


// GUEST/USER ROUTES (PROTECTED BY AUTH)
Route::middleware('auth')->group(function () {
    // 1. Redirection logic: Regular users go to their reservations list
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        // Guest users are redirected to their custom reservations index
        return redirect()->route('guest.reservations.index');
    })->name('dashboard');

    // 2. Room Viewing (Guest can only see available rooms)
    Route::get('/available-rooms', [RoomViewerController::class, 'available'])->name('rooms.available');

    // 3. Guest Reservation Management
    Route::get('/my-reservations', [GuestReservationController::class, 'index'])->name('guest.reservations.index');
    Route::get('/my-reservations/create', [GuestReservationController::class, 'create'])->name('reservations.create-guest');
    Route::post('/my-reservations', [GuestReservationController::class, 'store'])->name('reservations.store-guest');
    // Cancel a guest reservation
    Route::patch('/my-reservations/{reservation}/cancel', [GuestReservationController::class, 'cancel'])
        ->name('guest.reservations.cancel');
    // Guest viewing their own reservation details and invoice
    Route::get('/my-reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.my-show');

    // 4. Guest Customer Profile Edit (To fill in phone/passport details)
    Route::get('/customer/profile/edit', [CustomerController::class, 'editGuestProfile'])->name('customer.edit-guest-profile');
    Route::put('/customer/profile', [CustomerController::class, 'updateGuestProfile'])->name('customer.update-guest-profile');


    // Profile routes (standard Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications/read-all', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.readAll');
});


// ADMIN ROUTES (PROTECTED BY ROLE:ADMIN)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Rooms CRUD
    Route::resource('rooms', RoomController::class);

    // Customers CRUD
    Route::resource('customers', CustomerController::class);

    // Reservations CRUD (Admin can view/edit ALL)
    Route::resource('reservations', ReservationController::class);
    Route::get('/my-reservations/{reservation}/invoice', [ReservationController::class, 'invoice'])->name('reservations.invoice-guest');
    Route::get('reservations/{id}/invoice-pdf', [ReservationController::class, 'printInvoice'])->name('reservations.invoice.pdf');



    // User Role Management (NEW)
    Route::resource('users', UserController::class);

    // Invoice Viewing (Admin uses the standard reservations.invoice)
    Route::get('/reservations/{reservation}/invoice', [ReservationController::class, 'invoice'])->name('reservations.invoice');
});

// Logout route (standard)
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

require __DIR__ . '/auth.php';
