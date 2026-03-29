<?php
// app/Http/Controllers/Api/JoinController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JoinController extends Controller
{
    /**
     * Kiểm tra mã mời (không cần đăng nhập)
     */
    public function checkCode($code)
    {
        try {
            $classroom = Classroom::where('invite_code', strtoupper($code))
                ->with('teacher')
                ->first();
            
            if (!$classroom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã mời không hợp lệ'
                ], 404);
            }
            
            if (!$classroom->invite_expires_at || $classroom->invite_expires_at < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã đã hết hạn. Vui lòng liên hệ giáo viên để được cấp mã mới.'
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'class_id' => $classroom->id,
                    'class_name' => $classroom->name,
                    'teacher_name' => $classroom->teacher->name,
                    'student_count' => $classroom->students()->count(),
                    'expires_at' => $classroom->invite_expires_at
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tham gia lớp bằng mã mời
     */
    public function joinByCode(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'student') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ học sinh mới có thể tham gia lớp'
                ], 403);
            }
            
            $request->validate([
                'invite_code' => 'required|string|size:8'
            ]);
            
            $code = strtoupper($request->invite_code);
            
            $classroom = Classroom::where('invite_code', $code)->first();
            
            if (!$classroom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã mời không hợp lệ'
                ], 404);
            }
            
            if (!$classroom->invite_expires_at || $classroom->invite_expires_at < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã đã hết hạn. Vui lòng liên hệ giáo viên để được cấp mã mới.'
                ], 400);
            }
            
            // Kiểm tra đã tham gia chưa
            $alreadyJoined = $classroom->students()->where('user_id', $user->id)->exists();
            
            if ($alreadyJoined) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã tham gia lớp này rồi'
                ], 400);
            }
            
            // Thêm học sinh vào lớp
            $classroom->students()->attach($user->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Tham gia lớp thành công',
                'data' => [
                    'classroom' => [
                        'id' => $classroom->id,
                        'name' => $classroom->name,
                        'teacher_name' => $classroom->teacher->name
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách lớp đã tham gia của học sinh
     */
    public function myClasses()
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'student') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ học sinh mới có thể xem lớp đã tham gia'
                ], 403);
            }
            
            $classes = $user->enrolledClasses()
                ->with('teacher')
                ->withCount('students')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $classes
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rời khỏi lớp
     */
    public function leaveClass($classId)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'student') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ học sinh mới có thể rời lớp'
                ], 403);
            }
            
            $classroom = Classroom::findOrFail($classId);
            
            $alreadyJoined = $classroom->students()->where('user_id', $user->id)->exists();
            
            if (!$alreadyJoined) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa tham gia lớp này'
                ], 400);
            }
            
            $classroom->students()->detach($user->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Rời lớp thành công'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}