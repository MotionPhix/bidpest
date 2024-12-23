<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

  Route::get(
    '/auth/{provider}/redirect',
    [\App\Http\Controllers\Social\ProviderController::class, 'redirectToProvider']
  )->name('socialite.redirect');

  Route::get(
    '/auth/{provider}/callback',
    [\App\Http\Controllers\Social\ProviderController::class, 'handleProviderCallback']
  )->name('socialite.callback');

  Route::get(
    '/auth/login',
    [\App\Http\Controllers\Auth\PasswordlessLoginController::class, 'create']
  )->name('auth.get.credentials');

  Route::post(
    '/auth/login',
    [\App\Http\Controllers\Auth\PasswordlessLoginController::class, 'login']
  )->name('auth.log.user');

  Route::get(
    '/auth/verify-token',
    [\App\Http\Controllers\Auth\PasswordlessLoginController::class, 'get_token']
  )->name('auth.get.token');

  Route::post(
    '/auth/verify-token',
    [\App\Http\Controllers\Auth\PasswordlessLoginController::class, 'verifyToken']
  )->name('auth.verify.token');

});

Route::middleware('auth')->group(function () {

  Route::get(
    'verify-email',
    EmailVerificationPromptController::class
  )->name('verification.notice');

  Route::get(
    'verify-email/{id}/{hash}',
    VerifyEmailController::class
  )->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

  Route::post(
    'email/verification-notification',
    [EmailVerificationNotificationController::class, 'store']
  )->middleware('throttle:6,1')
    ->name('verification.send');

  Route::post(
    'logout',
    [AuthenticatedSessionController::class, 'destroy']
  )->name('logout');

  Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

});
