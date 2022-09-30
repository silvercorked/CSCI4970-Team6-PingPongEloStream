<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

use App\Models\User;

class PlayerController extends Controller {
    
    public function index(Request $request) {
        $props = self::getDefaultProps();
        return Inertia::render('Player/Index/Index', $props->all());
    }
    public function show(Request $request, $player_id) {
        $props = self::getDefaultProps();
        $player = User::with(['teams', 'teams.games', 'teams.elos', 'teams.currentElo'])->find($player_id);
        $teams = $player->teams;
        $props->put('player', $player);
        $props->put('teams', $teams);
        return Inertia::render('Player/Show/Show', $props->all());
    }
    public function profileShow(Request $request) { // colin
        $props = self::getDefaultProps();
        $player = auth()->user()->load(['teams', 'teams.games', 'teams.elos', 'teams.currentElo'])->get();
        $teams = $player->teams;
        $props->put('player', $player);
        $props->put('teams', $teams);
        $props->put('playerIsUser', true);
        return Inertia::render('Player/Show/Show', $props->all());
    }
    public function editProfile(Request $request) {
        $props = self::getDefaultProps();
        $user = auth()->user();
        $props->put('editable', [
            'name' => $user->name,
            'email' => $user->email
        ]);
    }
    public function updateProfile(Request $request) {
        
    }
}
