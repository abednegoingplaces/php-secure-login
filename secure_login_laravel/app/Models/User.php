<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Fields a form may fill. "role" is left out on purpose,
     * so nobody can make themselves an admin through the register form.
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /** Never include these when the model is turned into an array or JSON. */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login'        => 'datetime',
            'password'          => 'hashed', // hashed with bcrypt automatically when set
        ];
    }
}
