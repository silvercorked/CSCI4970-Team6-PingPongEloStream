<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\GameController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\LivestreamController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
    Other routes from Laravel Jetstream and Laravel Fortify:
    POST      _ignition/execute-solution ........................... ignition.executeSolution › Spatie\LaravelIgnition › ExecuteSolutionController
    GET|HEAD  _ignition/health-check ....................................... ignition.healthCheck › Spatie\LaravelIgnition › HealthCheckController
    POST      _ignition/update-config .................................... ignition.updateConfig › Spatie\LaravelIgnition › UpdateConfigController
    GET|HEAD  dashboard ................................................................................................................ dashboard
    GET|HEAD  forgot-password ............................................ password.request › Laravel\Fortify › PasswordResetLinkController@create
    POST      forgot-password ............................................... password.email › Laravel\Fortify › PasswordResetLinkController@store
    GET|HEAD  login .............................................................. login › Laravel\Fortify › AuthenticatedSessionController@create
    POST      login ....................................................................... Laravel\Fortify › AuthenticatedSessionController@store
    POST      logout ........................................................... logout › Laravel\Fortify › AuthenticatedSessionController@destroy
    GET|HEAD  register .............................................................. register › Laravel\Fortify › RegisteredUserController@create
    POST      register .......................................................................... Laravel\Fortify › RegisteredUserController@store
    POST      reset-password ..................................................... password.update › Laravel\Fortify › NewPasswordController@store
    GET|HEAD  reset-password/{token} ............................................. password.reset › Laravel\Fortify › NewPasswordController@create
    GET|HEAD  sanctum/csrf-cookie .............................................. sanctum.csrf-cookie › Laravel\Sanctum › CsrfCookieController@show
    GET|HEAD  two-factor-challenge ........................... two-factor.login › Laravel\Fortify › TwoFactorAuthenticatedSessionController@create
    POST      two-factor-challenge ............................................... Laravel\Fortify › TwoFactorAuthenticatedSessionController@store
    DELETE    user ...................................................... current-user.destroy › Laravel\Jetstream › CurrentUserController@destroy
    GET|HEAD  user/confirm-password ......................................................... Laravel\Fortify › ConfirmablePasswordController@show
    POST      user/confirm-password ..................................... password.confirm › Laravel\Fortify › ConfirmablePasswordController@store
    GET|HEAD  user/confirmed-password-status .................... password.confirmation › Laravel\Fortify › ConfirmedPasswordStatusController@show
    POST      user/confirmed-two-factor-authentication ... two-factor.confirm › Laravel\Fortify › ConfirmedTwoFactorAuthenticationController@store
    DELETE    user/other-browser-sessions ............ other-browser-sessions.destroy › Laravel\Jetstream › OtherBrowserSessionsController@destroy
    PUT       user/password ................................................... user-password.update › Laravel\Fortify › PasswordController@update
    GET|HEAD  user/profile ......................................................... profile.show › Laravel\Jetstream › UserProfileController@show
    PUT       user/profile-information ................... user-profile-information.update › Laravel\Fortify › ProfileInformationController@update
    DELETE    user/profile-photo ................................. current-user-photo.destroy › Laravel\Jetstream › ProfilePhotoController@destroy
    POST      user/two-factor-authentication ....................... two-factor.enable › Laravel\Fortify › TwoFactorAuthenticationController@store
    DELETE    user/two-factor-authentication .................... two-factor.disable › Laravel\Fortify › TwoFactorAuthenticationController@destroy
    GET|HEAD  user/two-factor-qr-code ...................................... two-factor.qr-code › Laravel\Fortify › TwoFactorQrCodeController@show
    GET|HEAD  user/two-factor-recovery-codes .......................... two-factor.recovery-codes › Laravel\Fortify › RecoveryCodeController@index
    POST      user/two-factor-recovery-codes ...................................................... Laravel\Fortify › RecoveryCodeController@store
    GET|HEAD  user/two-factor-secret-key ............................. two-factor.secret-key › Laravel\Fortify › TwoFactorSecretKeyController@show
    
    Controller Locations:
    Spatie\LaravelIgnition => \vendor\spatie\laravel-ignition\src\Http\Controllers
    Laravel\Fortify => \vendor\laravel\fortify\src\Http\Controllers
    Laravel\Jetstream => \vendor\laravel\jetstream\src\Http\Controllers\Inertia
    Code in these controllers are not to be edited, as they are in the vendor file.

*/


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
});

// public
Route::group([], function() {
    // landing/login
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    });
    Route::get('/leaderboards', [LeaderboardController::class, 'index'])->name('leaderboards');
    Route::get('/games', [GameController::class, 'index'])->name('game.index');
    Route::get('/players', [PlayerController::class, 'index'])->name('player.index');
    Route::get('/games/{game_id}', [GameController::class, 'show'])->name('game.show');
    Route::get('/players/{player_id}', [PlayerController::class, 'show'])->name('player.show');
});
// auth
Route::group(['middleware' => ['auth:sanctum', config('jetstream.auth_session'), 'verified']], function() {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    //Route::get('/profile', [PlayerController::class, 'renderProfile'])->name('profile.show');
    Route::get('/profile/edit', [PlayerController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [PlayerController::class, 'updateProfile'])->name('profile.update');
});
// admin
Route::group(['middleware' => ['auth:sanctum', config('jetstream.auth_session'), 'verified', 'isAdmin']], function() {
    Route::get('/livestream', [LivestreamController::class, 'index'])->name('livestream');
    Route::get('/games/create', [GameController::class, 'create'])->name('game.create');
    Route::get('/games/{game_id}/edit', [GameController::class, 'edit'])->name('game.edit');
    Route::post('/games', [GameController::class, 'store'])->name('game.store');
    Route::post('/games/play', [GameController::class, 'storeAndPlay'])->name('games.storeAndPlay');
    Route::put('/games/{game_id}', [GameController::class, 'update'])->name('game.update');
    Route::get('/games/{game_id}/start-play', [GameController::class, 'play'])->name('game.start');
    Route::get('/games/{game_id}/playing', [GameController::class, 'playing'])->name('game.playing');
});
