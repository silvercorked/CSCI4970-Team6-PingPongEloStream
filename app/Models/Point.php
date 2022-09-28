<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Set;
use App\Models\Team;

class Point extends Model {
    use HasFactory;

    public function set() {
        return $this->belongsTo(
            Set::class, 'set_id', 'id'
        );
    }
    public function team() {
        return $this->belongsTo(
            Team::class, 'scoring_team_id', 'id'
        );
    }
}
