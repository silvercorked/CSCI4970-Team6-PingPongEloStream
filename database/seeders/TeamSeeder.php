<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Team;
use App\Models\User;

class TeamSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $users = User::all();
        foreach($users as $user) {
            $team = new Team();
            $team->save();
            $team->members()->attach($user);
        }
        $memberGroups = self::getAllTeamCombos($users);
        for ($i = 0; $i < count($memberGroups); $i += 3) { // skip every few so there arent so many
            $team = new Team();
            $team->save();
            $team->members()->attach($memberGroups[$i]);
        }
    }
    private static function getAllTeamCombos($users) {
        $ret = collect();
        for ($i = 0; $i < $users->count() - 1; $i++) {
            $user = $users[$i];
            for ($j = $i + 1; $j < $users->count(); $j++) {
                $user2 = $users[$j];
                $ret->push(collect([$user->id, $user2->id]));
            }
        }
        return $ret;
    }
}
