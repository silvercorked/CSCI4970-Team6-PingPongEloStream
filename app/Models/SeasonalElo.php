<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

use App\Models\Season;
use App\Models\Team;

class SeasonalElo extends Pivot {
    use HasFactory;

    /**
     * Specifies the table to use for this model.
     *
     * @var string
     */
    public $table = 'seasonal_elos';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function team() {
        return $this->belongsTo(
            Team::class, 'team_id', 'id'
        );
    }
    public function season() {
        return $this->belongsTo(
            Season::class, 'season_id', 'id'
        );
    }
}
