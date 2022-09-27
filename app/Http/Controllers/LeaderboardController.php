<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Season;
use App\Models\Team;

class LeaderboardController extends Controller {
    
    public function index() {
        $season = Season::current();
        $props = self::getDefaultProps();
        $teams = Team::with([
            'members',
            'elos' => function($q) use ($season) {
                $q->where('season_id', $season->id);
            }
        ])->get()->map(function($t) {
            $t->elo = $t->elos->first()->elo;
            return $t;
        })->sortByDesc('elo', SORT_NUMERIC)->values()->all();

        $props->put('teams', $teams);
        $props->put('season_number', $season->id);
        return Inertia::render('Leaderboards/Leaderboards', $props->all());
    }
}
