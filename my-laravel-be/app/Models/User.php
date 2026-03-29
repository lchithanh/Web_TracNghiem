<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_code',
        'avatar',
        'email_verified_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    // app/Models/User.php
// Kiểm tra các relationship

public function exams()
{
    return $this->hasMany(Exam::class, 'created_by');
}

public function classrooms()
{
    return $this->hasMany(Classroom::class, 'teacher_id');
}


    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    public function taughtClasses()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    public function enrolledClasses()
    {
        return $this->belongsToMany(Classroom::class, 'class_students', 'user_id', 'class_id');
    }
    
    public function createdSubjects()
    {
        return $this->hasMany(Subject::class, 'created_by');
    }
    
    public function createdExams()
    {
        return $this->hasMany(Exam::class, 'created_by');
    }
}