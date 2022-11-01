<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Mode;

class ModeSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $single = new Mode();
        $single->name = 'Singles';
        $single->team_count = 2;
        $single->player_per_team_count = 1;
        $single->win_score = 11;
        $single->set_count = 3;
        $single->serve_switch = 2;
        $single->tie_serve_switch_override = 'low-score';
        $double = new Mode();
        $double->name = 'Doubles';
        $double->team_count = 2;
        $double->player_per_team_count = 2;
        $double->win_score = 21;
        $double->set_count = 3;
        $double->serve_switch = 5;
        $double->tie_serve_switch_override = 'low-score';
        $single->save();
        $double->save();
    }
}
