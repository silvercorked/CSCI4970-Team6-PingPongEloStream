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
        $season = Season::orderBy('id', 'desc')->first();
        $teams = Team::all();
        foreach ($teams as $team) {
            $team->seasons()->attach($season, ['elo' => mt_rand(1000, 2000)]);
        }
    }
}
