<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Validator;

use App\Models\Game;
use App\Models\Mode;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;

class GameController extends Controller {
    public function base(Request $request, int $season_id = null) {
        $page = $request->get('page'); // grab page from queryString
        $size = $request->get('size');
        $type = $request->get('type');
        if ($page || $size)
            return $this->paginated($request, $page ?? 1, $size ?? 15, $type, $season_id);
        else
            return $this->all($request, $type, $season_id);
    }
    public function all(Request $request, string|null $type, int|null $season_id) {
        $baseQuery = Game::with([
            'teams' => function ($q) {
                $q->withPivot('set_score');
            }, 'teams.members',
        ])->orderBy('updated_at');
        if ($season_id)
            $baseQuery->where('season_id', $season_id);
        $result;
        if (!$type) // if type not set, do pagination for all
            $result = $baseQuery->get();
        else if ($type == 'scheduled') // can't be unstarted but complete, so only 1 where needed
            $result = $baseQuery->where('started_at', null)->get();
        else if ($type == 'playing')
            $result = $baseQuery->where('started_at', '!=', null)->where('completed_at', null)->get();
        else if ($type == 'completed')
            $result = $baseQuery->where('completed_at', '!=', null)->get();
        else
            return self::unsuccessfulResponse('Allowed types are \'\', \'scheduled\', \'playing\', \'completed\'.');
        return self::successfulResponse([
            'games' => $result
        ]);
    }
    public function paginated(Request $request, int $page, int $size, string|null $type, int|null $season_id) {
        if ($page < 0) $page = 1;
        $page--; // page 1 starts at offset 0, so page X is actually offset $size * (x - 1)
        if ($size <= 0) $size = 15;
        $baseQuery = self::getPaginated($page, $size, Game::with([
            'teams' => function ($q) {
                $q->withPivot('set_score');
            }, 'teams.members',
        ]))->orderBy('updated_at');
        if ($season_id)
            $baseQuery->where('season_id', $season_id);
        $result;
        if (!$type) // if type not set, do pagination for all
            $result = $baseQuery->get();
        else if ($type == 'scheduled') // can't be unstarted but complete, so only 1 where needed
            $result = $baseQuery->where('started_at', null)->get();
        else if ($type == 'playing')
            $result = $baseQuery->where('started_at', '!=', null)->where('completed_at', null)->get();
        else if ($type == 'completed')
            $result = $baseQuery->where('completed_at', '!=', null)->get();
        else
            return self::unsuccessfulResponse('Allowed types are \'\', \'scheduled\', \'playing\', \'completed\'.');
        return self::successfulResponse([
            'games' => $result
        ]);
    }
    public function getOne(Request $request, $game_id) {
        $game = Game::with(['sets', 'sets.points', 'teams', 'teams.members'])->find($game_id);
        return self::successfulResponse([
            'game' => $game
        ]);
    }
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'mode_id' => ['required', 'exists:modes,id'],
            'team1_id' => ['required', 'exists:teams,id'],
            'team2_id' => ['required', 'exists:teams,id', 'different:team1_id'],
            'team1_first_server_id' => ['required', 'exists:users,id'],
            'team2_first_server_id' => ['required', 'exists:users,id'],
            'first_server' => ['required', 'regex:/^(team1)|(team2)$/']
        ]);
        if ($validator->fails())
            return self::unsuccessfulResponse($validator->errors());
        $mode = Mode::find($request->only('mode_id')['mode_id']);
        $teams = Team::findMany($request->only(['team1_id', 'team2_id']));
        $user1 = User::find($request->only(['team1_first_server_id'])['team1_first_server_id']);
        $user2 = User::find($request->only(['team2_first_server_id'])['team2_first_server_id']);
        $game = new Game();
        $game->first_server = $request->only(['first_server']) == 'team1';
        $game->mode()->associate($mode->id);
        $game->season()->associate(Season::current()->id);
        $game->team1FirstServer()->associate($user1->id);
        $game->team2FirstServer()->associate($user2->id);
        $game->save();
        $game->teams()->attach($teams->first()->id, [
            'set_score' => 0,
            'team_number' => 1
        ]);
        $game->teams()->attach($teams->last()->id, [
            'set_score' => 0,
            'team_number' => 2
        ]);
        $game->save();
        return self::successfulResponse([
            'game' => $game
        ]);
    }
    public function update(Request $request, $game_id) {
        Validator::make($request->all(), [
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
            return self::unsuccessfulResponse('Game is complete and cannot be replayed.');
        if ($game->started_at)
            return self::unsuccessfulResponse('Game has already been started.');
        $game->started_at = Carbon::now();
        $game->save();
        return self::successfulResponse([
            'game' => $game
        ]);
    }
    public function progress() {

    }
    public function complete(Request $request, $game_id) {
        $game = Game::find($game_id);
        if (!$game->started_at)
            return self::unsuccessfulResponse('Game cannot be completed as it hasn\'t yet started.');
        if ($game->complete_at) {
            return self::unsuccessfulResponse('Game has already been completed.');
        }
        $game->completed_at = Carbon::now();
        $game->save();
        return self::successfulResponse([
            'game' => $game
        ]);
    }
}
