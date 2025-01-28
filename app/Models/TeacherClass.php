<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherClass extends Model
{
    protected $fillable = ['user_id', 'year', 'class_name'];

    public function teacher()
    {
        return $this->belongsTo(User::class);
    }
}
