<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductionScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrVerificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffSessionController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\TicketReviewController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\VisionController;
use App\Http\Controllers\WalkInOrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\EnsureStaffTerminalUnlocked;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public & Auth Routes
Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/shop', fn () => view('shop'))->name('shop');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::get('/email/verify', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return view('auth.verify-email');
})->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('orders.index');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Customer Base Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/ticket/{orderTicket}', [OrderController::class, 'showTicket'])->name('orders.ticket');
    Route::get('/orders/verify/{orderTicket}', [OrderController::class, 'showTicket'])->name('tickets.verify');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/challenge', [ChallengeController::class, 'notice'])->name('challenge.notice');
    Route::post('/challenge/verify', [ChallengeController::class, 'verify'])->name('challenge.verify');
    Route::post('/challenge/resend', [ChallengeController::class, 'resend'])->name('challenge.resend');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/dashboard/link-phone', [ProfileController::class, 'sendPhoneOtp'])->name('profile.link.phone');
    Route::get('/dashboard/verify-otp', [ProfileController::class, 'showOtpForm'])->name('profile.otp.form');
    Route::post('/dashboard/verify-otp', [ProfileController::class, 'verifyOtp'])->name('profile.otp.verify');

    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::delete('/settings/delete', [SettingsController::class, 'destroy'])->name('settings.destroy');

    Route::get('/settings/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/settings/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::post('/settings/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');

    // Dedicated Tickets Directory Route
    Route::get('/tickets', [OrderController::class, 'ticketsIndex'])->name('tickets.index');

    // Order & Ticket Management Routes
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/ticket/{orderTicket}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/ticket/{orderTicket}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/ticket/{orderTicket}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('/tickets/{id}/restore', [OrderController::class, 'restore'])->name('tickets.restore');

    Route::get('/community/create', [CommunityController::class, 'create'])->name('community.post.create');
    Route::post('/community/post', [CommunityController::class, 'storePost'])->name('community.post.store');
    Route::post('/community/post/{id}/comment', [CommunityController::class, 'storeComment'])->name('community.comment.store');
    Route::post('/community/post/{id}/react', [CommunityController::class, 'toggleReaction'])->name('community.react');
    Route::get('/community/post/{id}/edit', [CommunityController::class, 'edit'])->name('community.post.edit');
    Route::put('/community/post/{id}', [CommunityController::class, 'update'])->name('community.post.update');
    Route::delete('/community/post/{id}', [CommunityController::class, 'destroy'])->name('community.post.destroy');
});

// Staff Portal Routes
Route::middleware(['auth', 'staff', EnsureStaffTerminalUnlocked::class])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [TicketReviewController::class, 'index'])->name('dashboard');

        Route::get('/qr-scanner', [QrVerificationController::class, 'index'])->name('qr.index');
        Route::post('/qr-scanner/verify', [QrVerificationController::class, 'verify'])->name('qr.verify');
        Route::post('/qr-scanner/complete', [QrVerificationController::class, 'completeHandoff'])->name('qr.complete');

        Route::get('/walkin-intake', [WalkInOrderController::class, 'create'])->name('walkin.create');
        Route::post('/walkin-intake', [WalkInOrderController::class, 'processForm'])->name('walkin.process');

        Route::get('/schedule', [ProductionScheduleController::class, 'index'])->name('schedule.index');

        Route::get('/transfers', [StockTransferController::class, 'index'])->name('transfers.index');
        Route::post('/transfers', [StockTransferController::class, 'storeTransfer'])->name('transfers.store');
        Route::post('/messages', [StockTransferController::class, 'sendMessage'])->name('messages.send');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

        // Terminal Session Lock Wall Routes
        Route::post('/session/lock', [StaffSessionController::class, 'lockSession'])->name('session.lock');
        Route::get('/session/locked', [StaffSessionController::class, 'showLockWall'])->name('session.locked');
        Route::post('/session/unlock', [StaffSessionController::class, 'unlockSession'])->name('session.unlock');
    });

// API & Community Public Routes
Route::get('/guest/order-prompt', function () {
    return view('guest.order-prompt');
})->name('guest.order.prompt');

Route::post('/api/vision/extract-colors', [VisionController::class, 'extractColors'])->name('vision.extract');
Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
Route::get('/community/user/{id}', [CommunityController::class, 'getUserProfileJson'])->name('community.user.profile');