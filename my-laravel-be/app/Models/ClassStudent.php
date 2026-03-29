<?php
// app/Models/ClassStudent.php

namespace App\Models;

use App\Models\Classroom;  // THÊM DÒNG NÀY
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassStudent extends Model
{
    use HasFactory;

    protected $table = 'class_students';

    protected $fillable = [
        'class_id',
        'user_id',
    ];

    public $timestamps = false;

    // Relationships
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}