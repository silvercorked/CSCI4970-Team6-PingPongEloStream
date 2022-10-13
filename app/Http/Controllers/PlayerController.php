<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

use App\Models\User;

class PlayerController extends Controller {

    public function all(Request $request) {
        $players = User::all();
        return self::successfulResponse([
            'players' => $players
        ]);
    }
    public function getOne(Request $request, $user_id) {
        $player = User::find($user_id);
        return self::successfulResponse([
            'player' => $player
        ]);
    }
    public function getProfileInfo(Request $request, $player_id) {
        $player = User::with(['teams', 'teams.games', 'teams.elos', 'teams.currentElo'])->find($player_id);
        $teams = $player->teams;
        return self::successfulResponse([
            'player' => $player,
            'teams' => $teams,
            // 'player_is_user' => $request->user()->id === $player->id
            'player_is_user' => auth()->user()->id === $player->id
        ]);
    }
    public function updateProfile(Request $request) {

    }
    public function getPlayerStats(Request $request, $player_id) {

        /*
        Current elo, leaderboard ranking, matches played, win rate
        */
        $player = User::where($player_id)->first();
        return $player;
    }
}

/*
I would like to make a route like /api/player/{id}/stats which returns data
such as
- elo (difficult because it needs to be elo of the singles team, in the current season)
- # of matches played (same as above: singles team, current season)
- win rate (i.e. get # of matches won)
- leaderboard ranking

not related to the stats route, but I also need to get matches, which involves:
- player 1 and player 2
- final scores
- resulting elos of both


I'm not even sure of the queries, let alone the laravel/eloquent code to accomplish them.
Where do I put the code? in models? in controllers?
*/