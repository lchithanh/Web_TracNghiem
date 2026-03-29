<?php
// app/Models/AttemptAnswer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttemptAnswer extends Model
{
    use HasFactory;

    protected $table = 'attempt_answers';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer_id',
    ];

    public $timestamps = false;

    // Relationships
    public function attempt()
    {
        return $this->belongsTo(Attempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }

    // Accessors
    public function getIsCorrectAttribute()
    {
        return $this->answer && $this->answer->is_correct;
    }
}