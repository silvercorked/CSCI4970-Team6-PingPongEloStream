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

    /**
     * Relationship function to gather the mode related to this game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation - belongs to (many-1) relationship
     */
    public function mode() {
        return $this->belongsTo(
            Mode::class, 'mode_id', 'id'
        );
    }
    /**
     * Relationship function to gather the season related to this game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation - belongs to (many-1) relationship
     */
    public function season() {
        return $this->belongsTo(
            Season::class, 'season_id', 'id'
        );
    }
    /**
     * Relationship function to gather the sets related to this game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation - has many (1-many) relationship
     */
    public function sets() {
        return $this->hasMany(
            Set::class, 'game_id', 'id'
        );
    }
    /**
     * Relationship function to gather the teams related to this game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation - belongs to many (many-many) relationship
     */
    public function teams() {
        return $this->belongsToMany(
            Team::class, 'games_teams_assoc', 'game_id', 'team_id', 'id', 'id'
        );
    }
    /**
     * Relationship function to gather the user who should/is/did serve first on team 1 related to this game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation - belongs to (many-1) relationship
     */
    public function team1FirstServer() {
        return $this->belongsTo(
            User::class, 'team1_first_server_id', 'id'
        );
    }
    /**
     * Relationship function to gather the user who should/is/did serve first on team 2 related to this game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation - belongs to (many-1) relationship
     */
    public function team2FirstServer() {
        return $this->belongsTo(
            User::class, 'team2_first_server_id', 'id'
        );
    }
    /**
     * Helper function to determine if team 1 serves first.
     *
     * @return bool - true if team 1 serves first; false otherwise
     */
    public function team1ServesFirst() {
        return $this->first_server;
    }
    /**
     * Helper function to determine if team 2 serves first.
     *
     * @return bool - true if team 2 serves first; false otherwise
     */
    public function team2ServesFirst() {
        return !$this->first_server;
    }
    /**
     * Relationship function to gather the team which is team 1 related to this game.
     *
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function team1() {
        return $this->belongsToMany(
            Team::class, 'games_teams_assoc', 'game_id', 'team_id', 'id', 'id'
        )->oldest()->first();
    }
    /**
     * Relationship function to gather the team which is team 2 related to this game.
     *
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function team2() {
        return $this->belongsToMany(
            Team::class, 'games_teams_assoc', 'game_id', 'team_id', 'id', 'id'
        )->latest()->first();
    }
    /**
     * Helper function to grab team 1 out of a collection.
     *
     * @param Collection $arr - array of teams.
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function pickTeam1(Collection $arr) {
        return self::pickTeam(1, $arr);
    }
    /**
     * Helper function to grab team 2 out of a collection.
     *
     * @param Collection $arr - array of teams.
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function pickTeam2(Collection $arr) {
        return self::pickTeam(2, $arr);
    }
    /**
     * Helper function to grab a team (determined by first param) out of a collection.
     *
     * @param int $which - the team number for the desired team.
     * @param Collection $arr - array of teams.
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private static function pickTeam(int $which, Collection $arr) {
        for ($i = 0; $i < $arr->count(); $i++) {
            $item = $arr->get($i);
            if ($item->pivot->team_number == $which)
                return $item;
        }
        return null;
    }
}
