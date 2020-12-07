<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    public $table = 'publisher';

    use HasFactory;

    public function User()
    {
        return $this->hasMany(User::class, 'id');
    }

    public function Req()
    {
        return $this->hasMany(RequestPublisher::class, 'publisher_id');
    }
}
