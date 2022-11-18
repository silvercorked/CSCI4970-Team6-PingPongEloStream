<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Validator;

use App\Models\User;

class UserController extends Controller {
    public function getSelf(Request $request) {
        return self::successfulResponse([
            'user' => $request->user()
        ]);
    }
    public function updateUser(Request $request) {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'photo' => [
                'required',
                File::image()
                    ->min(1) // 1KB
                    ->max(2 * 1024) // 2MB
            ]
        ]);
        if ($validator->fails())
            return self::unsuccessfulResponse($validator->errors());
        if (User::hasCustomPhoto($user) && Storage::disk('public')->exists($user->profile_photo_path)) { // need to delete old photo
            $res = Storage::disk('public')->delete($user->profile_photo_path);
        }
        $filename = now()->format('Y-m-d\TH-i-s-u\Z') . '.' . $request->file('photo')->extension();
        $user->profile_photo_path = $filename;
        $res = Storage::disk('public')->putFileAs(
            '',
            $request->file('photo'),
            $user->profile_photo_path
        );
        if (!$res)
            return self::unsuccessfulResponse('Failed to save image.');
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $user = User::find($user->id); // get latest
        return self::successfulResponse([
            'user' => $user
        ]);
    }
    public function deleteUser(Request $request) {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'password' => 'required|string'
        ]);
        if (!Hash::check($request->password, $user->password))
            return self::unsuccessfulResponse('Invalid Password');
        return self::unsuccessfulResponse('not yet implemented');
    }
}
