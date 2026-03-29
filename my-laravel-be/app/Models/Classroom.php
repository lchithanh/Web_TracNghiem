<?php
// app/Models/Classroom.php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;  // THÊM DÒNG NÀY

class Classroom extends Model
{
    use HasFactory;
    
    protected $table = 'classes';
    
    protected $fillable = [
        'name',
        'description',
        'teacher_id',
        'invite_code',
        'invite_expires_at',
    ];
    
    protected $casts = [
        'invite_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relationships
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    
    public function students()
    {
        return $this->belongsToMany(User::class, 'class_students', 'class_id', 'user_id');
    }
    
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_class', 'class_id', 'exam_id');
    }
    
    // Tạo mã mời khi tạo lớp
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($classroom) {
            $classroom->invite_code = self::generateUniqueCode();
            $classroom->invite_expires_at = Carbon::now()->addDays(30);  // SỬA: dùng Carbon::now()
        });
    }
    
    // Tạo mã duy nhất
    private static function generateUniqueCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('invite_code', $code)->exists());
        
        return $code;
    }
    
    // Kiểm tra mã còn hiệu lực
    public function isInviteValid()
    {
        return $this->invite_expires_at && $this->invite_expires_at > Carbon::now();  // SỬA: dùng Carbon::now()
    }
    
    // Tạo mã mới
    public function regenerateInviteCode()
    {
        $this->invite_code = self::generateUniqueCode();
        $this->invite_expires_at = Carbon::now()->addDays(30);  // SỬA: dùng Carbon::now()
        $this->save();
        return $this->invite_code;
    }
    
    // Cập nhật thời gian hết hạn
    public function extendInviteExpiry($days = 30)
    {
        $this->invite_expires_at = Carbon::now()->addDays($days);  // SỬA: dùng Carbon::now()
        $this->save();
        return $this->invite_expires_at;
    }
}