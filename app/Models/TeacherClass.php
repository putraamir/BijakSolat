<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherClass extends Model
{
    protected $fillable = ['user_id', 'year', 'class_name', 'class_room_id'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }
}
