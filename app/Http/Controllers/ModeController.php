<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Mode;

class ModeController extends Controller {
    public function all() {
        return self::successfulResponse(Mode::all());
    }
    public function getOne(Request $request, $mode_id) {
        $mode = Mode::find($mode_id);
        if (!$mode)
            return self::noResourceResponse();
        return self::successfulResponse($mode);
    }
}
