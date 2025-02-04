<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = ['name', 'year_id'];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_class');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereDoesntHave('teachers');
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
