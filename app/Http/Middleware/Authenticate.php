<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function unauthenticated($request, array $guards) {
        if (!$request->expectsJson())
            return abort(404);
        abort(response()->json([
            'response' => 'Invalid Token.',
            'success' => false
        ], 401));
    }
}
