<?php
// app/Models/Exam.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    // app/Models/Exam.php

protected $fillable = [
    'subject_id',
    'title',
    'description',
    'duration',
    'max_attempts',  // THÊM
    'total_questions',
    'created_by',
    'status',
    'start_time',
    'end_time',
];

    public $timestamps = false;

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    // Thêm quan hệ với lớp
    public function classes()
    {
        return $this->belongsToMany(Classroom::class, 'exam_class', 'exam_id', 'class_id');
    }
}