<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

use App\Models\Team;

class User extends Authenticatable {
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['profile_photo_url'];

    public static function hasCustomPhoto(User $u) {
        return !str_starts_with($u->profile_photo_path, 'https://xsgames.co/randomusers/assets/avatars/');
    }

    public function teams() {
        return $this->belongsToMany(
            Team::class, 'members', 'user_id', 'team_id', 'id', 'id'
        );
    }
    // has relation to games in which was first server via team1_first_server and team2_first_server

    protected function profilePhotoUrl(): Attribute {
        return Attribute::make(
            get: function () {
                if (User::hasCustomPhoto($this))
                    return env('APP_URL', '') . Storage::url($this->profile_photo_path);
                return $this->profile_photo_path; // default avatar
            }
        );
    }
}
