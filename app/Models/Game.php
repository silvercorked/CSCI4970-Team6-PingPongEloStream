<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Mode;
use App\Models\Season;
use App\Models\Set;
use App\Models\Team;

class Game extends Model {
    use HasFactory;

    public function mode() {
        return $this->belongsTo(
            Mode::class, 'mode_id', 'id'
        );
    }
    public function season() {
        return $this->belongsTo(
            Season::class, 'season_id', 'id'
        );
    }
    public function sets() {
        return $this->hasMany(
            Set::class, 'game_id', 'id'
        );
    }
    public function teams() {
        return $this->belongsToMany(
            Team::class, 'teams_games_assoc', 'game_id', 'team_id', 'id', 'id'
        );
    }
}
