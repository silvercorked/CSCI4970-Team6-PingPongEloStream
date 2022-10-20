<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\LivestreamController;
use App\Http\Controllers\LeaderboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// auth/login/creds routes
Route::group([], function() {
    Route::post('/register', [AuthController::class, 'register'])->name('create a new user and return a token');
    Route::post('/login', [AuthController::class, 'getToken'])->name('get user a token via login');
    Route::group(['middleware' => ['auth:sanctum']], function() {
        Route::post('/sign-out', [AuthController::class, 'destroyCurrentToken'])->name('destroy token being used currently');
        Route::post('/get-all-tokens', [AuthController::class, 'getAllTokens'])->name('gets all user tokens');
        Route::post('/full-sign-out', [AuthController::class, 'revokeAllTokens'])->name('destroy all this user\'s tokens');
    });
});

// public
Route::group([], function() {
    Route::get('/leaderboards/singles', [LeaderboardController::class, 'singles'])->name('grabs all singles teams (ordered by current season elo)');
    Route::get('/leaderboards/singles/season/{season_id}', [LeaderboardController::class, 'singles'])->name('grabs all singles teams (ordered by given season elo)');
    Route::get('/leaderboards/doubles', [LeaderboardController::class, 'doubles'])->name('grabs all doubles teams (ordered by current season elo)');
    Route::get('/leaderboards/doubles/season/{season_id}', [LeaderboardController::class, 'doubles'])->name('grabs all doubles teams (ordered by given season elo)');
    Route::get('/games', [GameController::class, 'all'])->name('get all games (ordered by most recent)');
    Route::get('/players', [PlayerController::class, 'all'])->name('get all players');
    Route::get('/games/{game_id}', [GameController::class, 'getOne'])->name('get one game');
    Route::get('/players/{player_id}', [PlayerController::class, 'getOne'])->name('get one player');
    Route::get('/players/{player_id}/teams', [PlayerController::class, 'getProfileInfo'])->name('get profile information for given player');
    Route::get('/players/{player_id}/teams/singles', [PlayerController::class, 'getSinglesTeamAndUser'])->name('get player and singles team for given player');
    Route::get('/players/{player_id}/teams/singles/ranking/season/{season_id}', [LeaderboardController::class, 'getPlayerSinglesRankingAndElo'])->name('get player\'s ranking on the leaderboards and elo for given season');
    Route::get('/teams/{team_id}/games/{season_id}', [TeamController::class, 'getTeamGames'])->name('get games for given team');
});
// auth
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/self', [UserController::class, 'getSelf'])->name('get the user\'s user info');
    Route::put('/profile', [PlayerController::class, 'updateProfile'])->name('profile.update');
});
// admin
Route::group(['middleware' => ['auth:sanctum', 'isAdmin']], function() {
    Route::get('/livestream', [LivestreamController::class, 'index'])->name('livestream');
    Route::get('/games/create', [GameController::class, 'create'])->name('game.create');
    Route::get('/games/{game_id}/edit', [GameController::class, 'edit'])->name('game.edit');
    Route::post('/games', [GameController::class, 'store'])->name('game.store');
    Route::post('/games/play', [GameController::class, 'storeAndPlay'])->name('games.storeAndPlay');
    Route::put('/games/{game_id}', [GameController::class, 'update'])->name('game.update');
    Route::get('/games/{game_id}/start-play', [GameController::class, 'play'])->name('game.start');
    Route::get('/games/{game_id}/playing', [GameController::class, 'playing'])->name('game.playing');
});
