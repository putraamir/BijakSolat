<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = ['name', 'year_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'teacher_class', 'class_id', 'teacher_id');
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
