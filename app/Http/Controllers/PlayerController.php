<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class PlayerController extends Controller {
    
    public function index(Request $request) {
        $props = self::getDefaultProps();
        return Inertia::render('Player/index', $props->all());
    }
    public function show(Request $request, $player_id) {

    }
    public function profileShow(Request $request) { // colin

    }
    public function editProfile(Request $request) {

    }
    public function updateProfile(Request $request) {

    }
}
