<?php
// app/Models/Attempt.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use HasFactory;

    protected $table = 'attempts';

    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'started_at',
        'submitted_at',
        'status',
        'time_spent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score' => 'float',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function answers()
    {
        return $this->hasMany(AttemptAnswer::class, 'attempt_id');
    }

    // Scopes
    public function scopeDoing($query)
    {
        return $query->where('status', 'doing');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    // Accessors
    public function getIsPassedAttribute()
    {
        return $this->score >= 5;
    }

    public function getGradeAttribute()
    {
        if ($this->score >= 8.5) return 'A+';
        if ($this->score >= 8.0) return 'A';
        if ($this->score >= 7.0) return 'B+';
        if ($this->score >= 6.0) return 'B';
        if ($this->score >= 5.0) return 'C+';
        if ($this->score >= 4.0) return 'C';
        return 'F';
    }

    public function getTimeSpentFormattedAttribute()
    {
        if (!$this->time_spent) return '00:00';
        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}