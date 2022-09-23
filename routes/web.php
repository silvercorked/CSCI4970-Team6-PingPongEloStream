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
    Route::get('/leaderboards', [LeaderboardController::class, 'renderLeaderboards'])->name('leaderboards');
    Route::get('/games', [GameController::class, 'renderGames'])->name('game.index');
    Route::get('/players', [PlayerController::class, 'renderPlayers'])->name('player.index');

    Route::get('/games/{game_id}', [GameController::class, 'renderGame'])->name('game.show');
    Route::get('/players/{player_id}', [PlayerController::class, 'renderPlayer'])->name('player.show');
});
// auth
Route::group(['middleware' => ['auth:sanctum', config('jetstream.auth_session'), 'verified']], function() {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/games/create', [GameController::class, 'renderCreateGame'])->name('game.create');
    Route::get('/games/{game_id}/edit', [GameController::class, 'renderEditGame'])->name('game.edit');
    Route::group([], function() { // for games create and edit
        Route::post('/games', [GameController::class, 'createGame'])->name('game.store');
        Route::put('/games/{game_id}', [GameController::class, 'updateGame'])->name('game.update');
    });
    //Route::get('/profile', [PlayerController::class, 'renderProfile'])->name('profile.show');
    Route::get('/profile/edit', [PlayerController::class, 'renderProfileEdit'])->name('profile.edit');
    Route::group([], function() {
        Route::put('/profile', [PlayerController::class, 'updateProfile'])->name('profile.update');
    });
});
// admin
Route::group(['middleware' => ['auth:sanctum', config('jetstream.auth_session'), 'verified', 'isAdmin']], function() {
    Route::get('/livestream', [LivestreamController::class, 'renderLivestream'])->name('livestream');
    Route::get('/games/{game_id}/start', [GameController::class, 'renderStartGame'])->name('game.begin');
    Route::group([], function() {
        Route::post('/games/{game_id}', [GameController::class, 'startGame'])->name('game.start');
    });
    Route::get('/games/{game_id}/play', [GameController::class, 'renderPlayingGame'])->name('game.playing');
});
