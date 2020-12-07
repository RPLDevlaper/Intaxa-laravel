<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;

    public function Magazine()
    {
        return $this->belongsTo(Magazine::class, 'id');
    }

    public function Topic()
    {
        return $this->belongsToMany(Topic::class, 'files_topics', 'file_id', 'topic_id');
    }
}
