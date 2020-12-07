<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryMagazine extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function Magazine()
    {
        return $this->hasMany(Magazine::class, 'category_id');
    }
}
