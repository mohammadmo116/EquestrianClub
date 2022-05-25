<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Trainer extends User
{
    use HasApiTokens, HasFactory, Notifiable,TwoFactorAuthenticatable;

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function horses()
    {
        return $this->morphMany(Horse::class,'owner');
    }
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('like');
    }
    protected $fillable = [
        'name',
        'email',
        'username',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

}
