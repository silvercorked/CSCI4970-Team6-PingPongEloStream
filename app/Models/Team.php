<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Game;
use App\Models\Season;
use App\Models\SeasonalElo;

class Team extends Model {
    use HasFactory;

    public function currentElo() {
        return $this->hasOne(
            SeasonalElo::class, 'team_id', 'id'
        )->latestOfMany();
    }

    public function members() {
        return $this->belongsToMany(
            User::class, 'members', 'team_id', 'user_id', 'id', 'id'
        );
    }
    public function games() {
        return $this->belongsToMany(
            Game::class, 'games_teams_assoc', 'team_id', 'game_id', 'id', 'id'
        );
    }
    public function elos() {
        return $this->hasMany(
            SeasonalElo::class, 'team_id', 'id'
        );
    }
    public function seasons() {
        return $this->belongsToMany(
            Season::class, 'seasonal_elos', 'team_id', 'season_id', 'id', 'id'
        )->withPivot('elo');
    }
}
