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
use Laravel\Socialite\Facades\Socialite;

Route::middleware('guest')->group(function () {

  Route::get(
    '/auth/{provider}/redirect',
    function (\Illuminate\Http\Request $request) {

      $providers = ['google', 'facebook'];

      if (!in_array($request->provider, $providers)) {
        return back()->withErrors([
          'error' => 'Invalid authentication provider'
        ]);
      }

      return Socialite::driver($request->provider)->redirect();
    }
  )->name('socialite.redirect');

  Route::get(
    '/auth/google/callback',
    [\App\Http\Controllers\Social\ProviderController::class, 'handleGoogleCallback']
  );

  Route::get(
    '/auth/facebook/callback',
    [\App\Http\Controllers\Social\ProviderController::class, 'handleFacebookCallback']
  );

  Route::get(
    '/auth/login',
    [\App\Http\Controllers\Auth\PasswordlessLoginController::class, 'create']
  )->name('login');

  Route::post(
    '/auth/login',
    [\App\Http\Controllers\Auth\PasswordlessLoginController::class, 'login']
  )->name('auth.log.user');

  Route::get(
    '/auth/verify/{user:uuid}',
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

  Route::resource(
    'bid-documents',
    \App\Http\Controllers\Bid\BidDocumentController::class
  );

  Route::post(
    '/bid-documents/{tenderDocument}/generate',
    [\App\Http\Controllers\Bid\BidDocumentController::class, 'generateBid']
  )->name('bids.generate');

  Route::post(
    '/tender-documents/{tenderDocument}/generate-bid',
    [\App\Http\Controllers\Tender\TenderController::class, 'generateBid']
  )->middleware(['auth', 'permission:create bid']);

  Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

});
