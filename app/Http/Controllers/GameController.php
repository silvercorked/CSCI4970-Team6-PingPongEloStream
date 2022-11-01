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

    public function all(Request $request) {
        $games = Game::with(['teams', 'teams.members'])->orderBy('updated_at')->get();
        return self::successfulResponse([
            'games' => $games
        ]);
    }
    public function getOne(Request $request, $game_id) {
        $game = Game::with(['sets', 'sets.points', 'teams', 'teams.members'])->find($game_id);
        return self::successfulResponse([
            'game' => $game
        ]);
    }
    public function store(Request $request) {
        $validator = Request::validate($request->all(), [
            'mode_id' => ['required', 'exists:modes,id'],
            'team1_id' => ['required', 'exists:teams,id'],
            'team2_id' => ['required', 'exists:teams,id', 'different:team1_id'],
            'team1_first_server_id' => ['required', 'exists:users,id'],
            'team2_first_server_id' => ['required', 'exists:users,id'],
            'first_server' => ['required', 'regex:/^(team1)|(team2)$/']
        ]);
        if ($validator->fails())
            return self::unsuccessfulResponse($validator->errors());
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
        return self::successfulResponse([
            'game' => $game
        ]);
    }
    public function storeAndPlay(Request $request) {
        $validator = Request::validate($request->all(), [
            'mode_id' => ['required', 'exists:modes,id'],
            'team1_id' => ['required', 'exists:teams,id'],
            'team2_id' => ['required', 'exists:teams,id', 'different:team1_id'],
            'team1_first_server_id' => ['required', 'exists:users,id'],
            'team2_first_server_id' => ['required', 'exists:users,id'],
            'first_server' => ['required', 'regex:/^(team1)|(team2)$/']
        ]);
        if ($validator->fails())
            return self::unsuccessfulResponse($validator->errors());
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
        return self::successfulResponse([
            'game' => $game
        ]);
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
        return self::successfulResponse([
            'game' => $game
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
        return self::successfulResponse([
            'game' => $game
        ]);
    }
}
