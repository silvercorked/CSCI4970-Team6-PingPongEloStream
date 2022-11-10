<?php

namespace App\Http\Controllers;

use DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

use App\Models\Season;
use App\Models\Team;
use App\Models\User;

class LeaderboardController extends Controller {
    public function getPlayerSinglesRankingAndElo(Request $request, $player_id, $season_id = null) {
        $player = User::find($player_id);
        $season = $season_id ? Season::find($season_id) : Season::current();
        $teams = self::getLeaderboardTeams(1, $season)->get();
        $team = $teams->filter(function ($t) use($player_id) {
            return $t->members[0]->id == $player_id;
        })->toArray();
        $ranking = array_keys($team)[0];
        $team = $team[$ranking];
        return self::successfulResponse([
            'team_id' => $team['id'],
            'elo' => $team['elo'],
            'ranking' => $ranking + 1
            // the items proprty of the collections this was extracted from is zero-indexed, so +1
        ]);
    }

    public function base(Request $request, string $which, int $season_id = null) {
        $season = $season_id ? Season::find($season_id) : Season::current();
        if (!$season)
            return self::noResourceResponse();
        $page = $request->get('page');
        $size = $request->get('size');
        if ($which == 'singles')
            if ($page || $size)
                return $this->paginatedSingles($request, $page ?? 1, $size ?? 15, $season);
            else
                return $this->singles($request, $season);
        else if ($which == 'doubles')
            if ($page || $size)
                return $this->paginatedDoubles($request, $page ?? 1, $size ?? 15, $season);
            else
                return $this->doubles($request, $season);
        else throw new ErrorException(
            'Regex passed weird value to leaderboards base',
            0,
            1,
            'LeaderboardController.php',
            29
        );
    }
    public function paginatedSingles(Request $request, int $page, int $size, Season $season) {
        return self::paginated($request, 1, $page, $size, $season);
    }
    public function paginatedDoubles(Request $request, int $page, int $size, Season $season) {
        return self::paginated($request, 2, $page, $size, $season);
    }
    private static function paginated(Request $request, int $playerCount, int $page, int $size, Season $season) {
        if ($page < 0) $page = 1;
        $page--; // page 1 starts at offset 0, so page X is actually offset $size * (x - 1)
        if ($size <= 0) $size = 15;
        $teams = self::getPaginated(
            $page,
            $size,
            self::getLeaderboardTeams($playerCount, $season)
        )->get();
        return self::successfulResponse([
            'teams' => self::mapResults($teams),
            'season_number' => $season->id
        ]);
    }
    public function singles(Request $request, Season $season) {
        return self::getAll($request, 1, $season);
    }
    public function doubles(Request $request, Season $season) {
        return self::getAll($request, 2, $season);
    }
    private static function getAll(Request $request, int $playerCount, Season $season) {
        $teams = self::getLeaderboardTeams($playerCount, $season)->get();
        return self::successfulResponse([
            'teams' => self::mapResults($teams),
            'season_number' => $season->id
        ]);
    }
    private static function mapResults(Collection $teams) {
        return $teams->map(fn ($team) => [
            'id' => $team->id,
            'members' => $team->members,
            'elo' => $team->elo
        ]);
    }
    public static function getLeaderboardTeams(int $teamSize, Season $season) {
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
