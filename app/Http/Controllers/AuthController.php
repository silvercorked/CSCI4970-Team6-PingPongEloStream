<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use Illuminate\Validation\Rules\Password;

use App\Models\User;
use App\Models\Season;
use App\Models\Team;
use App\Models\SeasonalElo;

class AuthController extends Controller {
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:1|max:255',
            'email' => 'required|email|unique:users,email|min:5|max:255',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
            'device_name' => 'required'
        ]);
        if ($validator->fails())
            return self::unsuccessfulResponse($validator->errors());
        $season = Season::current();
        $user = new User();
        $team = new Team();
        $elo = new SeasonalElo();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->profile_photo_path = 'https://xsgames.co/randomusers/assets/avatars/'
            . (rand(0, 1) == 1 ? 'male' : 'female')
            . '/' . rand(0, 74) . '.jpg'; // default image
        $user->save();
        $team->save();
        $elo->elo = 1500;
        $elo->season_id = $season->id;
        $elo->team_id = $team->id;
        $elo->save();
        $team->members()->attach($user->id);
        return self::successfulResponse([
            'token' => $user->createToken($request->device_name)->plainTextToken
        ]);
    }
    public function changePassword(Request $request) {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()]
        ]);
        if ($validator->fails())
            return self::unsuccessfulResponse($validator->errors());
        if (!Hash::check($request->current_password, $user->password))
            return self::unsuccessfulResponse([
                'current_password' => ['Invalid Password.']
            ]);
        $user->password = Hash::make($request->password);
        $user->save();
        return self::successfulResponse('Password Reset.');
    }
    public function getToken(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email|min:5|max:255',
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
            'tokens' => $user->tokens()->get()
                ->map(fn($item) => [
                    'device_name' => $item->name,
                    'last_used_at' => $item->last_used_at
                ])
        ]);
    }
    public function revokeAllTokens(Request $request) {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'password' => 'required|string'
        ]);
        if ($validator->fails())
            return self::unsuccessfulResponse($validator->errors());
        if (!Hash::check($request->password, $user->password))
            return self::unsuccessfulResponse([
                'password' => ['Invalid Password.']
            ]);
        $user->tokens()->delete();
        return self::successfulResponse([
            'message' => 'All Tokens successfully deleted.'
        ]);
    }
}
