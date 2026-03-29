<?php
// app/Models/Answer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'content',
        'is_correct',
    ];

    // TẮT timestamps vì bảng không có created_at, updated_at
    public $timestamps = false;

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}