<?php
// app/Http/Controllers/Api/StudentController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Lấy danh sách tất cả học sinh
     */
    public function index()
    {
        $students = User::where('role', 'student')
            ->select('id', 'name', 'email', 'student_code')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }
    
    /**
     * Tìm học sinh theo email
     */
    public function findByEmail($email)
    {
        $student = User::where('role', 'student')
            ->where('email', $email)
            ->first();
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy học sinh'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }
}