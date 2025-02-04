<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function teacherClasses()
    {
        return $this->hasMany(TeacherClass::class, 'user_id');
    }

    public function teacherClassRooms()
    {
        return $this->hasManyThrough(
            ClassRoom::class,
            TeacherClass::class,
            'user_id', // Foreign key on TeacherClass table
            'id', // Foreign key on ClassRoom table
            'id', // Local key on User table
            'class_room_id' // Local key on TeacherClass table
        );
    }
}
