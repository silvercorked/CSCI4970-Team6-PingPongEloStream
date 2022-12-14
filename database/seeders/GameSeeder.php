<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Game;
use App\Models\Mode;
use App\Models\Point;
use App\Models\Team;
use App\Models\Season;
use App\Models\Set;

use ELO;

class GameSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $this->fillSeason(1);
        $this->fillSeason(2);
        $this->scheduleGames(10);
        $this->startGames(2);
    }

    private function getTeams($teams, $teamCount) {
        $picked = $teams->random($teamCount);
        return Team::with('members', 'elos')->findMany($picked);
    }

    private function startGames(int $gamesToStart) {
        $games = Game::where('started_at', null)->inRandomOrder()->get();
        for ($i = 0; $i < $gamesToStart && $i < count($games); $i++) {
            $games[$i]->started_at = $games[$i]->created_at
                ->addSeconds(rand(
                    0,
                    Carbon::now()->diffInSeconds($games[$i]->started_at)
                )
            );
            $games[$i]->save();
        }
    }

    private function scheduleGames(int $gamesToSchedule) {
        $season = Season::current();
        $singles = Mode::find(1);
        $doubles = Mode::find(2);
        $teams = Team::with('members')->get();
        $singlesTeams = collect();
        $doublesTeams = collect();
        for ($i = 0; $i < count($teams); $i++) {
            $curr = $teams[$i];
            if (count($curr->members) == 1)
                $singlesTeams->add($curr->id);
            else // member count must be 2
                $doublesTeams->add($curr->id);
        }

        for ($i = 0; $i < $gamesToSchedule; $i++) {
            $g = new Game();
            $g->created_at = Carbon::today()
                ->subDays(
                    rand(1, 5))->addSeconds(rand(0, 86400));
            $mode = mt_rand(0, 1) ? $singles : $doubles;
            $teams = $this->getTeams($mode == $singles ? $singlesTeams : $doublesTeams, $mode->team_count);
            $g->mode()->associate($mode);
            $g->season()->associate($season);
            $g->first_server = mt_rand(0, 1) == 0; // true = team1. false = team2
            $team1FirstServer = $teams->first()->members[0];
            $team2FirstServer = $teams->last()->members[0];
            $g->team1FirstServer()->associate($team1FirstServer);
            $g->team2FirstServer()->associate($team2FirstServer);
            $g->save();
            $g->teams()->attach($teams->first(), [
                'set_score' => 0,
                'team_number' => 1
            ]);
            $g->teams()->attach($teams->last(), [
                'set_score' => 0,
                'team_number' => 2
            ]);
        }
    }

    private function fillSeason(int $season_id, int $howMany = 100) {
        $currSeason = Season::current();
        $season = Season::find($season_id);
        $singles = Mode::find(1);
        $doubles = Mode::find(2);
        $teams = Team::with('members')->get();
        $singlesTeams = collect();
        $doublesTeams = collect();
        for ($i = 0; $i < count($teams); $i++) {
            $curr = $teams[$i];
            if (count($curr->members) == 1)
                $singlesTeams->add($curr->id);
            else // member count must be 2
                $doublesTeams->add($curr->id);
        }

        for ($i = 0; $i < $howMany; $i++) {
            $g = new Game();
            $g->started_at = Carbon::today()
                ->subDays(
                    rand(
                        (180 * ($currSeason->id - $season->id)),
                        (180 * ($currSeason->id - $season->id)) + 179
                    ))->addSeconds(rand(0, 86400));
            $g->created_at = $g->started_at;
            $mode = mt_rand(0, 1) ? $singles : $doubles;
            $teams = $this->getTeams($mode == $singles ? $singlesTeams : $doublesTeams, $mode->team_count);
            $g->mode()->associate($mode);
            $g->season()->associate($season);
            $g->first_server = mt_rand(0, 1) == 0; // true = team1. false = team2
            $team1FirstServer = $teams->first()->members[0];
            $team2FirstServer = $teams->last()->members[0];
            $g->team1FirstServer()->associate($team1FirstServer);
            $g->team2FirstServer()->associate($team2FirstServer);
            $g->save();
            $team1SetScore = mt_rand(0, 2); // both game most are bo3
            $team2SetScore = $team1SetScore != 2 ? 2 : mt_rand(0, 1);
            $g->teams()->attach($teams->first(), [
                'set_score' => $team1SetScore,
                'team_number' => 1
            ]);
            $g->teams()->attach($teams->last(), [
                'set_score' => $team2SetScore,
                'team_number' => 2
            ]);
            $team1Wins = $team1SetScore > $team2SetScore;
            $team1Elo = $teams->first()->elos->filter(
                function ($e) use ($season_id) {
                    return $e->season_id == $season_id;
                }
            )->first();
            $team2Elo = $teams->last()->elos->filter(
                function ($e) use ($season_id) {
                    return $e->season_id == $season_id;
                }
            )->first();
            $newElos = ELO::EloRatingUpdate($team1Elo->elo, $team2Elo->elo, $team1Wins);
            //dd($newElos, $team1Elo, $team2Elo, $team1Wins);
            $g->team1_elo_change = $newElos[0] - $team1Elo->elo;
            $g->team2_elo_change = $newElos[1] - $team2Elo->elo;
            $g->team1_elo_then = $newElos[0];
            $g->team2_elo_then = $newElos[1];
            $team1Elo->elo = $newElos[0];
            $team2Elo->elo = $newElos[1];
            $team1Elo->save();
            $team2Elo->save();
            while ($team1SetScore + $team2SetScore > 0) {
                $set = new Set();
                $set->game()->associate($g);
                $tie = mt_rand(0, 10) == 0;
                $score1 = 0; $score2 = 0;
                if ($tie) {
                    $score1 = mt_rand($g->mode->win_score + 1, $g->mode->win_score + 8);
                    $score2 = $score1 - 2;
                }
                else {
                    $score1 = $g->mode->win_score;
                    $score2 = mt_rand(0, $score1 - 2);
                }
                if ($team1Wins) {
                    if ($team2SetScore > 0) {
                        $team2SetScore--;
                        $set->team1_score = $score2;
                        $set->team2_score = $score1;
                    }
                    else {
                        $team1SetScore--;
                        $set->team1_score = $score1;
                        $set->team2_score = $score2;
                    }
                }
                else {
                    if ($team1SetScore > 0) {
                        $team1SetScore--;
                        $set->team1_score = $score1;
                        $set->team2_score = $score2;
                    }
                    else {
                        $team2SetScore--;
                        $set->team1_score = $score2;
                        $set->team2_score = $score1;
                    }
                }
                $set->set_number = $team1SetScore + $team2SetScore + 1;
                $set->save();
                $team1WinsSet = $set->team1_score > $set->team2_score;
                while ($set->team1_score + $set->team2_score > 0) {
                    $point = new Point();
                    if ($set->team1_score > 0) {
                        $set->team1_score--;
                        $point->team()->associate($teams->first());
                    }
                    else {
                        $set->team2_score--;
                        $point->team()->associate($teams->last());
                    }
                    $point->set()->associate($set);
                    $point->save();
                }
            }
            $g->completed_at = $g->started_at->addSeconds(rand(60, 3600));
            $g->updated_at = $g->completed_at;
            $g->save();
        }
    }
}
