<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Season;
use App\Models\SeasonalElo;
use App\Models\Team;

class SeasonalEloSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $seasons = Season::orderBy('id', 'desc')->get();
        $teams = Team::all();
        foreach ($seasons as $season) {
            foreach ($teams as $team) {
                $team->seasons()->attach($season, ['elo' => 1500]);
            }
        }
    }
}
