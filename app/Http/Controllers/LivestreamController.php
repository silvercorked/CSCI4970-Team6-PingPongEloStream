<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Game;
use App\Models\Season;

class LivestreamController extends Controller {

    public function getLiveGame() {
        $season = Season::current();
        $liveGame = Game::whereNotNull('completed_at')->latest()->first();
        return self::successfulResponse([
            'current_game' => $liveGame,
            'season' => $season
        ]);
    }
}
