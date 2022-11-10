<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ModeController;
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
    Route::get('/leaderboards/{which}', [LeaderboardController::class, 'base'])
        ->where('which', '(singles)|(doubles)')->name('grabs all singles teams (ordered by current season elo) with optional pagination');
    Route::get('/leaderboards/{which}/season/{season_id?}', [LeaderboardController::class, 'base'])
        ->where('which', '(singles)|(doubles)')->name('grabs all singles teams (ordered by current season elo) with optional pagination');
    Route::get('/modes', [ModeController::class, 'all'])->name('get all modes');
    Route::get('/modes/{mode_id}', [ModeController::class, 'getOne'])->name('get one mode');
    Route::get('/games', [GameController::class, 'base'])->name('get all games (ordered by most recent)');
    Route::get('/games/season/{season_id}', [GameController::class, 'base'])->name('get set of games for pagination');
    Route::get('/players', [PlayerController::class, 'base'])->name('get all players (ordered by most recent) with optional pagination');
    Route::get('/games/{game_id}', [GameController::class, 'getOne'])->name('get one game');
    Route::get('/players/{player_id}', [PlayerController::class, 'getOne'])->name('get one player');
    Route::get('/players/{player_id}/teams', [PlayerController::class, 'getProfileInfo'])->name('get profile information for given player');
    Route::get('/players/{player_id}/teams/singles', [PlayerController::class, 'getSinglesTeamAndUser'])->name('get player and singles team for given player');
    Route::get('/players/{player_id}/teams/singles/ranking/season/{season_id}', [LeaderboardController::class, 'getPlayerSinglesRankingAndElo'])->name('get player\'s ranking on the leaderboards and elo for given season');
    Route::get('/teams', [TeamController::class, 'all'])->name('get all teams');
    Route::get('/teams/{team_id}', [TeamController::class, 'getOne'])->name('get a team');
    Route::post('/players/teams', [TeamController::class, 'getTeamFromPlayers'])->name('get teams that each have members containing all given players');
    Route::get('/teams/{team_id}/games/{season_id}', [TeamController::class, 'getTeamGames'])->name('get games for given team');
});
// auth
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/self', [UserController::class, 'getSelf'])->name('get the user\'s user info');
    Route::put('/profile', [PlayerController::class, 'updateProfile'])->name('edit user\'s profile');
    Route::post('/teams', [TeamController::class, 'store'])->name('create a team');
});
// admin
Route::group(['middleware' => ['auth:sanctum', 'isAdmin']], function() {
    Route::get('/livestream', [LivestreamController::class, 'index'])->name('livestream');
    Route::post('/games', [GameController::class, 'store'])->name('create a game');
    Route::put('/games/{game_id}', [GameController::class, 'update'])->name('edit a game that hasn\'t yet been played');
    Route::get('/games/{game_id}/play', [GameController::class, 'play'])->name('start playing a game');
    Route::post('/games/{game_id}/playing', [GameController::class, 'progress'])->name('report scores while a game is in progress');
    Route::post('/games/{game_id}/complete', [GameController::class, 'complete'])->name('complete a game');
});
