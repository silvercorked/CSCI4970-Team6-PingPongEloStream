<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;


class TeamController extends Controller {
    public function getTeamGames(Request $request, $team_id, $season_id) {
        $team = Team::find($team_id);

        $games = $team->games()->where('season_id', $season_id)->get();
        return self::successfulResponse([
            'games' => $games
        ]);


    }
}
