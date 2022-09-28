<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

use App\Models\Game;

class GameController extends Controller {
    
    public function index(Request $request) {
        $props = self::getDefaultProps();
        $games = Game::with(['teams', 'teams.members'])->orderBy('updated_at')->get();
        $props->put('games', $games);
        return Inertia::render('Games/Index', $props->all());
    }
    public function show(Request $request, $game_id) {
        $props = self::getDefaultProps();
        $game = Game::with(['sets', 'sets.points', 'teams', 'teams.members'])->find($game_id);
        $props->put('game', $game);
        return Inertia::render('Games/Show', $props->all());
    }
    public function create(Request $request) {
        $props = self::getDefaultProps();
        return Inertia::render('Games/Create', $props->all());
    }
    public function store(Request $request) {

    }
    public function edit(Request $request, $game_id) {

    }
    public function update(Request $request, $game_id) {

    }
    public function playing(Request $request, $game_id) {

    }
}
