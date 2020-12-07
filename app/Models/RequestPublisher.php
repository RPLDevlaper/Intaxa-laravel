<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestPublisher extends Model
{
    use HasFactory;

    public function User()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function Publisher()
    {
        return $this->belongsTo(Publisher::class, 'id');
    }
}
