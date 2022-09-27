<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Game;
use App\Models\Season;

class LivestreamController extends Controller {
    
    public function index() {
        $props = self::getDefaultProps();
        $props->put('season_number', Season::current()->id);
        $props->put('current_game', Game::where('complete', false)->latest()->first());
        return Inertia::render('Livestream/Livestream', $props->all());
    }
}
