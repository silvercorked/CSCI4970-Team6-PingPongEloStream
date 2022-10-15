<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Game;
use App\Models\Team;

class TeamController extends Controller {
    public function getTeamGames(Request $request, $team_id, $season_id) {
        $team = Team::find($team_id);
        if (!$team)
            return self::noResourceResponse();
        $games = Game::whereHas('teams', function($q) use ($team) {
            $q->where('team_id', $team->id);
        })->with(['teams' => function ($q) {
            $q->withPivot(['set_score', 'team_number']);
        }, 'teams.members'])->get();
        return self::successfulResponse([
            'games' => $games->map(function ($g) use ($team_id) {
                $team1 = Game::pickTeam1($g->teams);
                $team2 = Game::pickTeam2($g->teams);
                $given = $team1;
                $opponent = $team2;
                $givenIsTeam1 = $team1->id == $team_id;
                $givenServedFirst = (!$g->first_server && $givenIsTeam1) || ($g->first_server && !$givenIsTeam1);
                if (!$givenIsTeam1) {
                    $given = $team2;
                    $opponent = $team1;
                }
                return [
                    'id' => $g->id,
                    'mode_id' => $g->mode_id,
                    'season_id' => $g->season_id,
                    'started_at' => $g->started_at,
                    'completed_at' => $g->completed_at,
                    'created_at' => $g->created_at,
                    'updated_at' => $g->updated_at,
                    'given_team' => [
                        'id' => $given->id,
                        'set_score' => $given->pivot->set_score,
                        'served_first' => $givenServedFirst,
                        'first_server_id' => $givenIsTeam1
                            ? $g->team1_first_server_id
                            : $g->team2_first_server_id
                    ],
                    'opponent_team' => [
                        'id' => $opponent->id,
                        'set_score' => $opponent->pivot->set_score,
                        'served_first' => !$givenServedFirst,
                        'first_server_id' => $givenIsTeam1
                            ? $g->team2_first_server_id
                            : $g->team1_first_server_id
                    ]
                ];
            })
        ]);
    }
}
