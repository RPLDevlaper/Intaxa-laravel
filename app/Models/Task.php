<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public function User()
    {
        return $this->belongsToMany(User::class);
    }

    public function getDueDateAttribute()
    {
        $date = Carbon::create($this->attributes['due_date'])->isoFormat('dddd, D MMMM Y');
        return $date;
    }
}
