<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryTopic extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function Topic()
    {
        return $this->hasMany(Topic::class, 'category_id');
    }
}
