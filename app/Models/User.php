<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getAvatarAttribute($value)
    {
        if ($value) {
            return $value;
        }
        // Return a default avatar URL if no avatar is set
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function teacherClasses()
    {
        return $this->hasMany(TeacherClass::class);
    }

    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'teacher_class', 'teacher_id', 'class_id');
    }
}
