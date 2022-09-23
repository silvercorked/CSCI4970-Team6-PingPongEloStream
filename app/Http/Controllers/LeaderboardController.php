<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Models\Season;
use App\Models\Team;

class LeaderboardController extends Controller {
    
    public function renderLeaderboards() {
        $props = Auth::check()
        ? [
             'user' => Auth::user(),
        ]
        : [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ];
        $season = Season::current();
        $props['teams'] = Team::with([
            'members',
            'elos' => function($q) use ($season) {
                $q->where('season_id', $season->id);
        }])->get()->map(function($t) {
            $t->elo = $t->elos->first()->elo;
            return $t;
        })->sortByDesc('elo', SORT_NUMERIC)->values()->all();
        $props['season_number'] = $season->id;
        return Inertia::render('Leaderboards/Leaderboards', $props);
    }
}
