<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Teacher extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'teacher_class', 'teacher_id', 'class_id');
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, ClassRoom::class);
    }
}
