<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Set;

class Point extends Model {
    use HasFactory;

    public function set() {
        return $this->belongsTo(
            Set::class, 'set_id', 'id'
        );
    }
}
