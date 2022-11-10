<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function noResourceResponse($message = 'No resource found.', $status = 404) {
        return self::unsuccessfulResponse($message, $status);
    }
    public static function successfulResponse($message, $statusCode = 200) {
        return self::createResponse($message, true, $statusCode);
    }
    public static function unsuccessfulResponse($message, $statusCode = 400) {
        return self::createResponse($message, false, $statusCode);
    }

    /**
     * Formulates information from the API into a consistently shaped response.
     *
     * @param mixed     $message
     * @param bool      $success
     * @param int       $statusCode
     * @return \Illuminate\Support\Facades\Response
     */
    public static function createResponse($message, $success, $statusCode) {
        return response()->json([
            'response' => $message,
            'success' => $success
        ], $statusCode);
    }

    public static function getPaginated(int $page, int $size, Model|Builder $m): Builder {
        if ($m instanceof Model)
            return $m::offset($size * $page)->limit($size);
        else if ($m instanceof Builder)
            return $m->offset($size * $page)->limit($size);
        else throw new ErrorException(
            'First param must be of type Model or Builder',
            0,
            1,
            'Controller.php',
            45
        );
    }
}
