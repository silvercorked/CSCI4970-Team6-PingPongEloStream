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
        $single->win_score = 11;
        $single->set_count = 3;
        $single->serve_switch = 2;
        $single->tie_serve_switch_override = 'low-score';
        $double = new Mode();
        $double->name = 'Doubles';
        $double->win_score = 21;
        $double->set_count = 3;
        $double->serve_switch = 5;
        $double->tie_serve_switch_override = 'low-score';
        $single->save();
        $double->save();
    }
}
