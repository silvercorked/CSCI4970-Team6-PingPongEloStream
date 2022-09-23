<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Models\Game;
use App\Models\Season;

class LivestreamController extends Controller {
    
    public function renderLivestream() {
        $props = [
             'user' => Auth::user(),
             'season_number' => Season::current()->id,
             'current_game' => Game::where('complete', false)->latest()->first()
        ];
        return Inertia::render('Livestream/Livestream', $props);
    }
}
