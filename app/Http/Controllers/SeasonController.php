<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Season;

class SeasonController extends Controller {
    public function getCurrentSeason() {
        $s = Season::current();
        return self::successfulResponse([
            'season_number' => $s->id,
            'created_at' => $s->created_at,
            'updated_at' => $s->updated_at
        ]);
    }
    public function getAll() {
        return self::successfulResponse(Season::all()->map(function ($s) {
            return [
                'season_number' => $s->id,
                'created_at' => $s->created_at,
                'updated_at' => $s->updated_at
            ];
        }));
    }
}
