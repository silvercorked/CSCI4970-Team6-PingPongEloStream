<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Games;
use App\Models\Team;

class Season extends Model {
    use HasFactory;

    public function games() {
        return $this->hasMany(
            Game::class, 'season_id', 'id'
        );
    }
    public function teams() {
        return $this->belongsToMany(
            Team::class, 'seasonal_elos', 'season_id', 'team_id', 'id', 'id'
        );
    }
}
