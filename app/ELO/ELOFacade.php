<?php

namespace App\ELO;

use Illuminate\Support\Facades\Facade;

class ELOFacade extends Facade {
    protected static function getFacadeAccessor() {
        return 'elo';
    }
}
