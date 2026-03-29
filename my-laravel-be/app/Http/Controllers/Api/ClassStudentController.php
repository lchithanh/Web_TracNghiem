<?php
// app/Http/Controllers/Api/ClassStudentController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\ClassStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClassStudentController extends Controller
{
    /**
     * Danh sách học sinh trong lớp
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = ClassStudent::with(['classroom', 'student']);
            
            if ($request->has('classroom_id')) {
                $classroomId = $request->classroom_id;
                $classroom = Classroom::find($classroomId);
                
                // Kiểm tra quyền
                if ($user->role === 'teacher' && $classroom && $classroom->teacher_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền xem học sinh của lớp này'
                    ], 403);
                }
                
                $query->where('class_id', $classroomId);
            }
            
            $classStudents = $query->get();
            
            return response()->json([
                'success' => true,
                'data' => $classStudents
            ]);
            
        } catch (\Exception $e) {
            Log::error('ClassStudent index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách học sinh theo lớp (dùng route riêng)
     */
    public function getByClassroom($classroomId)
    {
        try {
            $user = Auth::user();
            
            // Kiểm tra lớp tồn tại
            $classroom = Classroom::find($classroomId);
            if (!$classroom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy lớp học'
                ], 404);
            }
            
            // Kiểm tra quyền
            if ($user->role === 'teacher' && $classroom->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem học sinh của lớp này'
                ], 403);
            }
            
            // Lấy danh sách học sinh
            $students = DB::table('class_students')
                ->where('class_id', $classroomId)
                ->join('users', 'class_students.user_id', '=', 'users.id')
                ->select(
                    'class_students.id',
                    'users.id as student_id',
                    'users.name',
                    'users.email'
                )
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $students
            ]);
            
        } catch (\Exception $e) {
            Log::error('getByClassroom error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thêm học sinh vào lớp
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'class_id' => 'required|exists:classes,id',
                'student_id' => 'required|exists:users,id',
            ]);
            
            $classroom = Classroom::find($validated['class_id']);
            
            if ($user->role !== 'teacher' || $classroom->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thêm học sinh vào lớp này'
                ], 403);
            }
            
            $student = User::find($validated['student_id']);
            if ($student->role !== 'student') {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng không phải là học sinh'
                ], 400);
            }
            
            $exists = ClassStudent::where('class_id', $validated['class_id'])
                ->where('user_id', $validated['student_id'])
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Học sinh đã có trong lớp'
                ], 400);
            }
            
            $classStudent = ClassStudent::create([
                'class_id' => $validated['class_id'],
                'user_id' => $validated['student_id'],
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Thêm học sinh thành công',
                'data' => $classStudent
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa học sinh khỏi lớp
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            $classStudent = ClassStudent::with('classroom')->findOrFail($id);
            
            if ($user->role !== 'teacher' || $classStudent->classroom->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa học sinh khỏi lớp này'
                ], 403);
            }
            
            $classStudent->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa học sinh thành công'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}