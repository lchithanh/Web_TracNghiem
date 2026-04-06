<?php
// app/Http/Controllers/Api/TeacherManagerController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeacherManagerController extends Controller
{
    /**
     * Lấy danh sách tất cả giáo viên kèm số môn học
     */
    public function index()
    {
        try {
            $teachers = User::withCount('subjects')
                ->where('role', 'teacher')
                ->select('id', 'name', 'email')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $teachers
            ]);
        } catch (\Exception $e) {
            Log::error("TeacherManagerController@index error: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server, không thể lấy danh sách giáo viên'
            ], 500);
        }
    }

    /**
     * Lấy danh sách môn học của giáo viên theo ID
     */
    public function getSubjects($teacherId)
    {
        try {
            $teacher = User::with('subjects')
                ->where('role', 'teacher')
                ->findOrFail($teacherId);

            return response()->json([
                'success' => true,
                'data' => $teacher->subjects
            ]);
        } catch (\Exception $e) {
            Log::error("TeacherManagerController@getSubjects error: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giáo viên hoặc lỗi server'
            ], 404);
        }
    }
}