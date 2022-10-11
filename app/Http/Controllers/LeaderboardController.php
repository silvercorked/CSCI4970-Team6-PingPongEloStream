<?php

namespace App\Http\Controllers;

use DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

use App\Models\Season;
use App\Models\Team;

class LeaderboardController extends Controller {

    public function singles(Request $request, $season_id = null) {
        $season = $season_id ? Season::find($season_id) : Season::current();
        $teams = $this->getLeaderboardTeams(1, $season)->get();
        return self::successfulResponse([
            'teams' => $teams->map(fn ($team) => [
                'id' => $team->id,
                'members' => $team->members,
                'elo' => $team->elo
            ]),
            'season_number' => $season->id
        ]);
    }
    public function doubles(Request $request, $season_id = null) {
        $season = $season_id ? Season::find($season_id) : Season::current();
        $teams = $teams = $this->getLeaderboardTeams(2, $season)->get();
        return self::successfulResponse([
            'teams' => $teams->map(fn ($team) => [
                'id' => $team->id,
                'members' => $team->members,
                'elo' => $team->elo
            ]),
            'season_number' => $season->id
        ]);
    }
    private function getLeaderboardTeams(int $teamSize, Season $season) {
        return Team::select(['teams.*', 'seasonal_elos.elo'])
            ->join('seasonal_elos', 'teams.id', '=', 'seasonal_elos.team_id')
            ->where('seasonal_elos.season_id', $season->id)
            ->join('members', 'teams.id', '=', 'members.team_id')
            ->groupBy('members.team_id')
            ->groupBy('seasonal_elos.elo')
            ->havingRaw('COUNT(members.user_id) = ' . $teamSize)
            ->orderBy('seasonal_elos.elo', 'desc')
            ->with('members');
    }
}
