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

class GameSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $this->fillSeason(1);
        $this->fillSeason(2);
    }

    private function fillSeason($season_id) {
        $season = Season::find($season_id);
        $singles = Mode::find(1);
        $doubles = Mode::find(2);

        for ($i = 0; $i < 150; $i++) {
            $g = new Game();
            $g->started_at = Carbon::now();
            $teams = Team::with(['members'])->inRandomOrder()->limit(2)->get();
            $g->mode()->associate($teams->reduce(function ($agg, $t) {
                $total = count($t->members);
                if ($agg < $total)
                    $agg = $total;
                return $agg;
            }) >= 2 ? $doubles : $singles);
            $g->season()->associate($season);
            $g->first_server = mt_rand(0, 1) == 0; // true = team1. false = team2
            $team1FirstServer = $teams->first()->members[0];
            $team2FirstServer = $teams->last()->members[0];
            $g->team1FirstServer()->associate($team1FirstServer);
            $g->team2FirstServer()->associate($team2FirstServer);
            $g->save();
            $team1SetScore = mt_rand(0, 2);
            $team2SetScore = mt_rand(0, $team1SetScore != 2 ? 2 : 1);
            $g->teams()->attach($teams->first(), [
                'set_score' => $team1SetScore
            ]);
            $g->teams()->attach($teams->last(), [
                'set_score' => $team2SetScore
            ]);
            $team1Wins = $team1SetScore > $team2SetScore;
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
                        $point->team()->associate(Game::pickTeam1($g->teams));
                    }
                    else {
                        $set->team2_score--;
                        $point->team()->associate(Game::pickTeam2($g->teams));
                    }
                    $point->set()->associate($set);
                    $point->save();
                }
            }
            $g->completed_at = Carbon::now();
            $g->save();
        }
    }
}
