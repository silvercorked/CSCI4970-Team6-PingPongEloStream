<?php

namespace App\Http\Controllers;

use DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

use App\Models\Season;
use App\Models\Team;

class LeaderboardController extends Controller {
    
    public function singles() {
        $season = Season::current();
        $props = self::getDefaultProps();
        $teams = Team::select(['teams.*', 'seasonal_elos.elo'])
            ->join('seasonal_elos', 'teams.id', '=', 'seasonal_elos.team_id')
            ->where('seasonal_elos.season_id', $season->id)
            ->join('members', 'teams.id', '=', 'members.team_id')
            ->groupBy('members.team_id')
            ->groupBy('seasonal_elos.elo')
            ->havingRaw('COUNT(members.user_id) = 1')
            ->orderBy('seasonal_elos.elo', 'desc')
            ->with('members')
            ->paginate(4)->through(function ($team) {
                return [
                    'id' => $team->id,
                    'members' => $team->members,
                    'elo' => $team->elo
                ];
            });
        $props->put('teams', $teams);
        $props->put('season_number', $season->id);
        return Inertia::render('Leaderboards/Singles/Singles', $props->all());
    }
    public function doubles() {
        $season = Season::current();
        $props = self::getDefaultProps();
        $teams = Team::select(['teams.*', 'seasonal_elos.elo'])
            ->join('seasonal_elos', 'teams.id', '=', 'seasonal_elos.team_id')
            ->where('seasonal_elos.season_id', $season->id)
            ->join('members', 'teams.id', '=', 'members.team_id')
            ->groupBy('members.team_id')
            ->groupBy('seasonal_elos.elo')
            ->havingRaw('COUNT(members.user_id) = 2')
            ->orderBy('seasonal_elos.elo', 'desc')
            ->with('members')
            ->paginate(4)->through(function ($team) {
                return [
                    'id' => $team->id,
                    'members' => $team->members,
                    'elo' => $team->elo
                ];
            });
        $props->put('teams', $teams);
        $props->put('season_number', $season->id);
        return Inertia::render('Leaderboards/Doubles/Doubles', $props->all());
    }
}
