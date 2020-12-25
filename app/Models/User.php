<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function myMagazine()
    {
        return $this->hasMany(Magazine::class, 'author_id');
    }

    public function Rating()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    public function Publisher()
    {
        return $this->belongsTo(Publisher::class, 'publisher_id');
    }

    public function Activity()
    {
        return $this->hasMany(Activity::class, 'user_id');
    }

    public function Req()
    {
        return $this->hasMany(RequestPublisher::class, 'user_id');
    }

    public function Task()
    {
        return $this->belongsToMany(Task::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
