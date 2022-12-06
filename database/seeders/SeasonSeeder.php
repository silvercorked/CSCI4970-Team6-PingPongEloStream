<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Season;

class SeasonSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $season = new Season();
        $season->created_at = now()->subMinutes(1);
        $season->updated_at = $season->created_at;
        $season->save();
        //$season2 = new Season();
        //$season2->save();
    }
}
