<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['name', 'email'];

    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'teacher_class')
            ->with('year');
    }
}
