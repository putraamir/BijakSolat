<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = ['name', 'year_id'];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'teacher_class', 'class_id', 'teacher_id');
    }
}
