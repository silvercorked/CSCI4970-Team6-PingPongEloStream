<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

use App\Models\Game;
use App\Models\Mode;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;

class GameController extends Controller {
    
    public function index(Request $request) {
        $props = self::getDefaultProps();
        $games = Game::with(['teams', 'teams.members'])->orderBy('updated_at')->get();
        $props->put('games', $games);
        return Inertia::render('Games/Index/Index', $props->all());
    }
    public function show(Request $request, $game_id) {
        $props = self::getDefaultProps();
        $game = Game::with(['sets', 'sets.points', 'teams', 'teams.members'])->find($game_id);
        $props->put('game', $game);
        return Inertia::render('Games/Show/Show', $props->all());
    }
    public function create(Request $request) {
        $props = self::getDefaultProps();
        return Inertia::render('Games/Create/Create', $props->all());
    }
    public function store(Request $request) {
        Request::validate($request->all(), [
            'mode_id' => 'required|exists:modes,id',
            'team1_id' => 'required|exists:teams,id',
            'team2_id' => 'required|exists:teams,id|different:team1_id',
            'team1_first_server_id' => 'required|exists:users,id',
            'team2_first_server_id' => 'required|exists:users,id',
            'first_server' => 'required|regex:/^(team1)|(team2)$/'
        ]);
        $mode = Mode::find($request->only('mode_id'));
        $teams = Team::findMany($request->only(['team1_id', 'team2_id']));
        $user1 = User::find($request->only(['team1_first_server_id']));
        $user2 = User::find($request->only(['team2_first_server_id']));
        $game = new Game();
        $game->first_server = $request->only(['first_server']) == 'team1';
        $game->mode()->associate($mode);
        $game->season()->associate(Season::current());
        $game->team1FirstServer()->associate($user1);
        $game->team2FirstServer()->associate($user2);
        $game->save();
        $game->teams()->attach($teams->first(), [
            'set_score' => 0
        ]);
        $game->teams()->attach($teams->last(), [
            'set_score' => 0
        ]);
        $game->save();
        return redirect('games.show', [
            'game_id' => $game->id
        ]);
    }
    public function storeAndPlay(Request $request) {
        Request::validate($request->all(), [
            'mode_id' => 'required|exists:modes,id',
            'team1_id' => 'required|exists:teams,id',
            'team2_id' => 'required|exists:teams,id|different:team1_id',
            'team1_first_server_id' => 'required|exists:users,id',
            'team2_first_server_id' => 'required|exists:users,id',
            'first_server' => 'required|regex:/^(team1)|(team2)$/'
        ]);
        $mode = Mode::find($request->only('mode_id'));
        $teams = Team::findMany($request->only(['team1_id', 'team2_id']));
        $user1 = User::find($request->only(['team1_first_server_id']));
        $user2 = User::find($request->only(['team2_first_server_id']));
        $game = new Game();
        $game->first_server = $request->only(['first_server']) == 'team1';
        $game->mode()->associate($mode);
        $game->season()->associate(Season::current());
        $game->team1FirstServer()->associate($user1);
        $game->team2FirstServer()->associate($user2);
        $game->save();
        $game->teams()->attach($teams->first(), [
            'set_score' => 0
        ]);
        $game->teams()->attach($teams->last(), [
            'set_score' => 0
        ]);
        $game->save();
        return redirect('games.start', [
            'game_id' => $game->id
        ]);
    }
    public function edit(Request $request, $game_id) {
        $props = self::getDefaultProps();
        $game = Game::find($game_id);
        if ($game->started_at || $game->completed_at)
            return redirect()->back()->withErrors([
                'edit' => 'Game is uneditable as it has ' . ($game->completed_at
                    ? 'been completed.'
                    : 'already started.')
            ]);
        return Inertia::render('Games/Edit/Edit', $props->all());
    }
    public function update(Request $request, $game_id) {
        Request::validate($request->all(), [
            'mode_id' => 'required|exists:modes,id',
            'team1_id' => 'required|exists:teams,id',
            'team2_id' => 'required|exists:teams,id|different:team1_id',
            'team1_first_server_id' => 'required|exists:users,id',
            'team2_first_server_id' => 'required|exists:users,id',
            'first_server' => 'required|regex:/^(team1)|(team2)$/'
        ]);
        $mode = Mode::find($request->only('mode_id'));
        $teams = Team::findMany($request->only(['team1_id', 'team2_id']));
        $user1 = User::find($request->only(['team1_first_server_id']));
        $user2 = User::find($request->only(['team2_first_server_id']));
        $game = Game::find($game_id);
        $game->first_server = $request->only(['first_server']) == 'team1';
        $game->mode()->associate($mode);
        $game->season()->associate(Season::current());
        $game->team1FirstServer()->associate($user1);
        $game->team2FirstServer()->associate($user2);
        $game->save();
        $game->teams()->sync($teams->first(), [
            'set_score' => 0
        ]);
        $game->teams()->sync($teams->last(), [
            'set_score' => 0
        ]);
        $game->save();
        return redirect('games.show', [
            'game_id' => $game->id
        ]);
    }
    public function play(Request $request, $game_id) {
        $game = Game::find($game_id);
        if ($game->completed_at)
            return redirect()->back()->withErrors([
                'play' => 'Game is unplayable as it has already been completed.'
            ]);
        if (!$game->started_at) { // if game not started, start it, regardless, send to playing
            $game->started_at = Carbon::now();
            $game->save();
        }
        return redirect('games.playing', [
            'game_id' => $game_id
        ]);
    }
    public function playing(Request $request, $game_id) {
        $props = self::getDefaultProps();
        $game = Game::find($game_id);
        $props->put('game', $game);
        if ($game->started_at && !$game->completed_at)
            return Inertia::render('Games/Playing/Playing', $props);
        return redirect()->back()->withErrors([
            'playing' => 'Game is not playable.'
        ]);
    }
}
