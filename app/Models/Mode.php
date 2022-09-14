<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Game;

class Mode extends Model {
    use HasFactory;

    public function games() {
        return $this->hasMany(
            Game::class, 'mode_id', 'id'
        );
    }
}
