<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'created_by'];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teachers()
    {
        return $this->belongsToMany(
            User::class,
            'teacher_subject',
            'subject_id',
            'teacher_id'
        )->where('role', 'teacher');
    }
}