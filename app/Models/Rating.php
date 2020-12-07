<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    public $table = 'user_magazines';

    protected $fillable = ['user_id', 'magazine_id', 'rating'];

    public function User()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function Magazine()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
