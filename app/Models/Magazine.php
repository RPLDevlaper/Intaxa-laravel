<?php

namespace App\Models;

use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Magazine extends Model
{
    use HasFactory;

    protected $fillable = ['author_id', 'title', 'description', 'cover', 'category_id'];

    public function Author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function Rating()
    {
        return $this->hasMany(Rating::class, 'magazine_id');
    }

    public function Category()
    {
        return $this->belongsTo(CategoryMagazine::class, 'category_id');
    }

    public function Topic()
    {
        return $this->hasMany(Topic::class, 'magazine_id');
    }

    public function File()
    {
        return $this->hasMany(Files::class, 'magazine_id');
    }

    public function getCreatedAtAttribute()
    {
        $date = Carbon::create($this->attributes['created_at'])->isoFormat('dddd, D MMMM Y');
        return $date;
    }
    public function getDeletedAtAttribute()
    {
        $date = Carbon::create($this->attributes['deleted_at'])->isoFormat('dddd, D MMMM Y');
        return $date;
    }

}
