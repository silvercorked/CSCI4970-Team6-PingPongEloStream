<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;

use App\Models\User;

class AuthController extends Controller {
    public function getToken(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|min:5|max:255',
            'password' => 'required',
            'device_name' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        if ($validator->fails() || !$user || !Hash::check($request->password, $user->password))
            return self::unsuccessfulResponse('Invalid Credentials.', 401);
        return self::successfulResponse([
            'token' => $user->createToken($request->device_name)->plainTextToken
        ]);
    }
    public function destroyCurrentToken(Request $request) {
        $user = auth()->user();
        $token = $user->currentAccessToken()->delete();
        return self::successfulResponse([
            'message' => 'Token successfully deleted.'
        ]);
    }
    public function getAllTokens(Request $request) {
        $user = auth()->user();
        return self::successfulResponse([
            'tokens' => $user->tokens()
                ->get()
                ->map(fn($item) => ['device_name' => $item->name])
        ]);
    }
    public function revokeAllTokens(Request $request) {
        $user = auth()->user();
        $user->tokens()->delete();
        return self::successfulResponse([
            'message' => 'All Tokens successfully deleted.'
        ]);
    }
}
