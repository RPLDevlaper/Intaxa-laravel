<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['magazine_id', 'cover', 'title', 'description', 'file_pdf', 'category_id'];

    public function Category()
    {
        return $this->belongsTo(Topic::class, 'category_id');
    }

    public function Magazine()
    {
        return $this->belongsTo(Magazine::class, 'magazine_id');
    }

    public function File()
    {
        return $this->belongsToMany(Files::class, 'files_topics', 'topic_id', 'file_id');
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
