<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller {
    public function getSelf(Request $request) {
        return self::successfulResponse([
            'user' => $request->user()
        ]);
    }
}
