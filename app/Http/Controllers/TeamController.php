<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Game;
use App\Models\Season;
use App\Models\SeasonalElo;
use App\Models\Team;
use App\Models\User;

class TeamController extends Controller {
    public function all() {
        return self::successfulResponse(Team::with('members')->all());
    }
    public function getOne(Request $request, $team_id) {
        $team = Team::with('members')->find($team_id);
        if (!$team)
            return self::noResourceResponse();
        return self::successfulResponse($team);
    }
    public function getTeamFromPlayers(Request $request) {
        $validator = Validator::make($request->all(), [
            'player_ids' => ['required', 'array'],
            'player_ids.*' => ['required', 'exists:users,id']
        ]);
        if ($validator->fails())
            return self::unsuccessfulResponse($validator->errors());
        $ids = $request->player_ids;
        $sizeIds = count($ids);
        return self::successfulResponse(
            Team::with('members')->whereHas('members',
                function ($q) use ($ids) {
                    $q->whereIn('user_id', $ids);
                },
                '>=', $sizeIds
            )->get()
        );
    }
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'player_ids' => ['required', 'array'],
            'player_ids.*' => ['required', 'exists:users,id', 'distinct']
        ]);
        if ($validator->fails())
            return self::unsuccessfulResponse($validator->errors());
        $ids = $request->player_ids;
        $sizeIds = count($ids);
        $teamDoesntExist = Team::with('members')->whereHas(
            'members',
            function ($q) use ($ids) {
                $q->whereIn('user_id', $ids);
            },
            '=',
            $sizeIds
        )->count() == 0;
        if (!$teamDoesntExist)
            return self::unsuccessfulResponse('Team already exists.');
        $season = Season::current();
        $team = new Team();
        $team->save();
        $elo = new SeasonalElo();
        $elo->elo = 1500;
        $elo->season_id = $season->id;
        $elo->team_id = $team->id;
        $elo->save();
        $team->members()->attach($ids);
        return self::successfulResponse($team);
    }
    public function getTeamGames(Request $request, $team_id, $season_id) {
        $team = Team::with('members')->find($team_id);
        if (!$team)
            return self::noResourceResponse();
        $games = Game::whereHas('teams', function($q) use ($team) {
            $q->where('team_id', $team->id);
        })->with([
            'teams' => function ($q) {
                $q->withPivot(['set_score', 'team_number']);
            },
            'teams.members'
        ])->where('completed_at', '!=', null)
        ->where('season_id', $season_id)
            ->orderByDesc('completed_at')->get();
        return self::successfulResponse([
            'team' => [
                'id' => $team->id,
                'members' => $team->members->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'name' => $m->name,
                        'email' => $m->email,
                        'profile_photo_url' => $m->profile_photo_url
                    ];
                })
            ],
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
                        'set_score' => $given->pivot->set_score,
                        'served_first' => $givenServedFirst,
                        'elo_after' => $givenIsTeam1
                            ? $g->team1_elo_then
                            : $g->team2_elo_then,
                        'elo_change' => $givenIsTeam1
                            ? $g->team1_elo_change
                            : $g->team2_elo_change,
                        'first_server_id' => $givenIsTeam1
                            ? $g->team1_first_server_id
                            : $g->team2_first_server_id
                    ],
                    'opponent_team' => [
                        'id' => $opponent->id,
                        'set_score' => $opponent->pivot->set_score,
                        'served_first' => !$givenServedFirst,
                        'elo_after' => $givenIsTeam1
                            ? $g->team2_elo_then
                            : $g->team1_elo_then,
                        'elo_change' => $givenIsTeam1
                            ? $g->team2_elo_change
                            : $g->team1_elo_change,
                        'first_server_id' => $givenIsTeam1
                            ? $g->team2_first_server_id
                            : $g->team1_first_server_id,
                        'members' => $opponent->members->map(function ($m) {
                            return [
                                'id' => $m->id,
                                'name' => $m->name,
                                'email' => $m->email,
                                'profile_photo_url' => $m->profile_photo_url
                            ];
                        })
                    ]
                ];
            })
        ]);
    }
}
