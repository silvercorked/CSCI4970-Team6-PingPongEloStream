<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Game;
use App\Models\Point;

class Set extends Model {
    use HasFactory;

    public function game() {
        return $this->belongsTo(
            Game::class, 'game_id', 'id'
        );
    }
    public function points() {
        return $this->hasMany(
            Point::class, 'set_id', 'id'
        );
    }
}
