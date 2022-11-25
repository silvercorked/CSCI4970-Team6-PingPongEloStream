<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Validator;
use ELO;

use App\Models\Game;
use App\Models\Mode;
use App\Models\Point;
use App\Models\Season;
use App\Models\Set;
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
        ])->orderByDesc('updated_at');
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
        ]))->orderByDesc('updated_at');
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
        $game = Game::with(['sets', 'sets.points', 'teams' => function ($q) {
            $q->withPivot('team_number')->orderBy('team_number');
        }, 'teams.members'])->find($game_id);
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
        $game->teams()->sync([
            $teams->first() => [
                'set_score' => 0,
                'team_number' => 1
            ], $teams->last() => [
                'set_score' => 0,
                'team_number' => 2
            ]
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
    public function progress(Request $request, int $game_id, int $set_number) {
        // check if exists & in progress.
        if ($set_number <= 0)
            return self::unsuccessfulResponse('Invalid set number.');
        $game = Game::with([
            'sets' => function ($q) {
                $q->orderBy('set_number');
            }, 'sets.points' => function ($q) {
                $q->orderBy('created_at');
            }, 'teams' => function ($q) {
                $q->withPivot(['team_number', 'set_score']);
            }, 'teams.members'
        ])->find($game_id);
        if (!$game)
            return self::noResourceResponse();
        if (!$game->started_at)
            return self::unsuccessfulResponse('Can\'t make changes to a game that hasn\'t started.');
        if ($game->completed_at)
            return self::unsuccessfulResponse('Can\'t make changes to a game that has been completed.');
        $teamIds = [Game::pickTeam1($game->teams)->id, Game::pickTeam2($game->teams)->id];
        $validator = Validator::make($request->all(), [
            'points' => 'present|array',
            'points.*' => [Rule::in($teamIds)]
        ]);
        if ($validator->fails())
            return self::unsuccessfulResponse($validator->errors());
        $setCount = count($game->sets);
        // update or create set
        if ($set_number <= $setCount) { // set already exists
            $set = $game->sets->filter(function ($s) use ($set_number) {
                return $s->set_number == $set_number;
            })->first();
            $this->populateSet($set, $request->points, $teamIds);
        }
        else if ($set_number == $setCount + 1 && $setCount < $game->mode->set_count) { // set not yet made and not too many sets according to mode
            $set = new Set();
            $setCount++;
            $set->set_number = $set_number;
            $set->game_id = $game->id;
            $this->populateSet($set, $request->points, $teamIds);
        }
        else
            return self::unsuccessfulResponse('Cannot create new set for this game.');
        // update set scores on team-game assoc
        $scores = [0, 0]; // will avoid counting score for latest game
        for ($i = 0; $i < $setCount - 1; $i++) { // as update may be an inprogress one
            $s = $game->sets[$i];
            $s->team1_score > $s->team2_score
                ? $scores[0]++
                : $scores[1]++;
        }
        $game->teams()->sync([
            $teamIds[0] => [
                'set_score' => $scores[0],
                'team_number' => 1
            ], $teamIds[1] => [
                'set_score' => $scores[1],
                'team_number' => 2
            ]
        ]);
        // save
        $game->save();
        return self::successfulResponse($game);
    }
    private function populateSet(Set &$set, Array $points, Array $teamIds) {
        $scores = [];
        for ($i = 0; $i < count($teamIds); $i++)
            $scores[$teamIds[$i]] = 0;
        $pointModels = [];
        $lenNewPoints = count($points);
        $lenOldPoints = count($set->points);
        for ($i = 0; $i < $lenNewPoints; $i++) {
            $point = $i < $lenOldPoints ? $set->points[$i] : new Point();
            $point->scoring_team_id = $points[$i];
            $scores[$point->scoring_team_id]++;
            array_push($pointModels, $point);
        }
        for ($i = $lenNewPoints; $i < $lenOldPoints; $i++) {
            $set->points[$i]->delete(); // any points beyond new point len can be deleted
        }
        $set->team1_score = $scores[$teamIds[0]];
        $set->team2_score = $scores[$teamIds[1]];
        $set->save();
        $set->points()->saveMany($pointModels);
    }
    public function complete(Request $request, $game_id) {
        $game = Game::with([
            'teams' => function ($q) {
                $q->withPivot(['team_number', 'set_score']);
            },
            'teams.elos',
            'teams.members',
            'mode',
            'sets',
            'sets.points'
        ])->find($game_id);
        if (!$game->started_at)
            return self::unsuccessfulResponse('Game cannot be completed as it hasn\'t yet started.');
        if ($game->completed_at)
            return self::unsuccessfulResponse('Game has already been completed.');
        // check if game is "complete" by game mode rules.
        $team1 = Game::pickTeam1($game->teams);
        $team2 = Game::pickTeam2($game->teams);
        $mode = $game->mode;
        $sets = $game->sets;
        $setCount = count($sets);
        $setScore = [0, 0];
        for ($i = 0; $i < $setCount; $i++) {
            $set = $sets[$i];
            $team1Score = 0; $team2Score = 0;
            for ($j = 0; $j < count($set->points); $j++) {
                if ($set->points[$j]->scoring_team_id == $team1->id)
                    $team1Score++;
                else
                    $team2Score++;
            }
            $team1WinSet = $team1Score > $team2Score;
            $highest = $team1WinSet ? $team1Score : $team2Score;
            if ($team1WinSet)
                $setScore[0]++;
            else
                $setScore[1]++;

            if (!(($team1WinSet && ($team1Score == $mode->win_score || $team1Score == $team2Score + 2))
                || (!$team1WinSet && ($team2Score == $mode->win_score || $team2Score == $team1Score + 2)))
            ) { // if team1 won and is mode win score or win by 2 (same for team 2) (all inverted)
                return self::unsuccessfulResponse('Highest score in set '
                    . $i + 1
                    . ' does not reach game mode requirement of '
                    . $mode->win_score
                    . ' points or is not a win by 2 points. Was '
                    . $team1Score . '-' . $team2Score . '.'
                );
            } // update aggregation values
            $set->team1_score = $team1Score;
            $set->team2_score = $team2Score;
        }
        $winningScore = intval($mode->set_count / 2) + 1;
        $team1Wins = $setScore[0] > $setScore[1];
        if (!(($team1Wins && $setScore[0] == $winningScore)
            || (!$team1Wins && $setScore[1] == $winningScore))
        )
            return self::unsuccessfulResponse('Invalid set score '
                . $setScore[0] . '-' . $setScore[1] .
                ' should have been best of ' . $mode->set_count
            );
        // update aggregation values
        for ($i = 0; $i < count($game->sets); $i++)
            $game->sets[$i]->save();
        $game->teams()->sync([
            $team1->id => [
                'set_score' => $setScore[0],
                'team_number' => 1
            ], $team2->id => [
                'set_score' => $setScore[1],
                'team_number' => 2
            ]
        ]);
        // run elo functions
        $team1Elo = $team1->elos->filter(
            function ($e) use ($game) {
                return $e->season_id == $game->season_id;
            }
        )->first();
        $team2Elo = $team2->elos->filter(
            function ($e) use ($game) {
                return $e->season_id == $game->season_id;
            }
        )->first();
        $newElos = ELO::EloRatingUpdate($team1Elo->elo, $team2Elo->elo, $team1Wins);
        $game->team1_elo_change = $newElos[0] - $team1Elo->elo;
        $game->team2_elo_change = $newElos[1] - $team2Elo->elo;
        $game->team1_elo_then = $newElos[0];
        $game->team2_elo_then = $newElos[1];
        $team1Elo->elo = $newElos[0];
        $team2Elo->elo = $newElos[1];
        $team1Elo->save();
        $team2Elo->save();
        // then save
        $game->completed_at = Carbon::now();
        $game->save();
        return self::successfulResponse([
            'game' => $game
        ]);
    }
}
