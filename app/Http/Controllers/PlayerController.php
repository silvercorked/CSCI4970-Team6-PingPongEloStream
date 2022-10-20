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
            'player_is_user' => $request->user()->id === $player->id
        ]);
    }
    public function updateProfile(Request $request) {

    }
    public function getSinglesTeamAndUser(Request $request, $player_id) {
        $player = User::with(['teams', 'teams.members'])->find($player_id);

        if (!$player) return self::noResourceResponse();

        $team = $player->teams->filter(function($team) {
            return count($team->members) == 1;
        })->first();
        return self::successfulResponse([
            'player' => [
                'id' => $player->id,
                'name' => $player->name
            ],
            'team_id' => $team->id
        ]);
    }
}
