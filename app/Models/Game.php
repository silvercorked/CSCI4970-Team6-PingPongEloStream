<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
            Team::class, 'games_teams_assoc', 'game_id', 'team_id', 'id', 'id'
        );
    }

    public function team1() {
        return $this->belongsToMany(
            Team::class, 'games_teams_assoc', 'game_id', 'team_id', 'id', 'id'
        )->oldest()->first();
    }
    public function team2() {
        return $this->belongsToMany(
            Team::class, 'games_teams_assoc', 'game_id', 'team_id', 'id', 'id'
        )->latest()->first();
    }
    public static function pickTeam1(Collection $arr) {
        return self::pickTeam(1, $arr);
    }
    public static function pickTeam2(Collection $arr) {
        return self::pickTeam(2, $arr);
    }
    private static function pickTeam(int $which, Collection $arr) {
        if ($arr->count() != 2) return null;
        if (Carbon::parse($arr->first()->created_at)->greaterThan(Carbon::parse($arr->last()->created_at))) { // true = 0 before 1
            return $which == 1 ? $arr->first() : $arr->last();
        }
        return $which == 1 ? $arr->last() : $arr->first();
    }
}
