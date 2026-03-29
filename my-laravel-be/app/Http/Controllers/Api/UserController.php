<?php
// app/Http/Controllers/Api/UserController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Exam;
use App\Models\Classroom;
use App\Models\Attempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Lấy danh sách tất cả người dùng
     */
    public function index()
    {
        try {
            $users = User::select('id', 'name', 'email', 'role', 'student_code')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('User index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Tạo người dùng mới
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'required|in:admin,teacher,student',
                'student_code' => 'nullable|string|max:20',
            ]);
            
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'student_code' => $validated['student_code'] ?? null,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Thêm người dùng thành công',
                'data' => $user
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('User store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Lấy chi tiết người dùng
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }
    }
    
    /**
     * Cập nhật người dùng
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:6',
                'role' => 'sometimes|in:admin,teacher,student',
                'student_code' => 'nullable|string|max:20',
            ]);
            
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }
            
            $user->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật người dùng thành công',
                'data' => $user
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Xóa người dùng - Chỉ admin mới có quyền
     */
    public function destroy($id)
    {
        try {
            $currentUser = Auth::user();
            
            if (!$currentUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập'
                ], 401);
            }
            
            $userToDelete = User::find($id);
            
            if (!$userToDelete) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng'
                ], 404);
            }
            
            if ($currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa người dùng'
                ], 403);
            }
            
            if ($currentUser->id == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa tài khoản của chính mình'
                ], 400);
            }
            
            $warnings = [];
            $relatedItems = [];
            
            // Sử dụng Model để kiểm tra dữ liệu liên quan
            if ($userToDelete->role === 'teacher') {
                // Kiểm tra bài thi - dùng relationship
                $examCount = $userToDelete->exams()->count();
                if ($examCount > 0) {
                    $exams = $userToDelete->exams()->select('id', 'title')->limit(5)->get();
                    $warnings[] = "Đã tạo {$examCount} bài thi";
                    $relatedItems['exams'] = [
                        'count' => $examCount,
                        'items' => $exams->toArray()
                    ];
                }
                
                // Kiểm tra lớp học - dùng relationship với bảng classes
                try {
                    $classroomCount = $userToDelete->classrooms()->count();
                    if ($classroomCount > 0) {
                        $classrooms = $userToDelete->classrooms()->select('id', 'name')->limit(5)->get();
                        $warnings[] = "Đã tạo {$classroomCount} lớp học";
                        $relatedItems['classrooms'] = [
                            'count' => $classroomCount,
                            'items' => $classrooms->toArray()
                        ];
                    }
                } catch (\Exception $e) {
                    Log::warning('Classrooms table error: ' . $e->getMessage());
                }
            }
            
            if ($userToDelete->role === 'student') {
                // Kiểm tra bài làm - dùng relationship
                $attemptCount = $userToDelete->attempts()->count();
                if ($attemptCount > 0) {
                    $attempts = $userToDelete->attempts()
                        ->with('exam:id,title')
                        ->select('id', 'exam_id', 'score', 'status')
                        ->limit(5)
                        ->get();
                    $warnings[] = "Đã làm {$attemptCount} bài thi";
                    $relatedItems['attempts'] = [
                        'count' => $attemptCount,
                        'items' => $attempts->toArray()
                    ];
                }
            }
            
            if (count($warnings) > 0) {
                $warningMessage = "Người dùng \"{$userToDelete->name}\" có dữ liệu liên quan:\n";
                foreach ($warnings as $warning) {
                    $warningMessage .= "• " . $warning . "\n";
                }
                $warningMessage .= "\n⚠️ Xóa người dùng sẽ MẤT tất cả dữ liệu này. Bạn có chắc chắn muốn xóa?";
                
                return response()->json([
                    'success' => false,
                    'message' => $warningMessage,
                    'requires_confirmation' => true,
                    'warnings' => $warnings,
                    'related_items' => $relatedItems
                ], 400);
            }
            
            // Không có dữ liệu liên quan, tiến hành xóa
            $userName = $userToDelete->name;
            $userToDelete->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Đã xóa người dùng \"{$userName}\" thành công"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete user error: ' . $e->getMessage());
            Log::error('Delete user trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Xóa người dùng kèm dữ liệu liên quan (force delete)
     */
    public function forceDestroy($id)
    {
        try {
            $currentUser = Auth::user();
            
            if (!$currentUser || $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            $userToDelete = User::find($id);
            
            if (!$userToDelete) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng'
                ], 404);
            }
            
            if ($currentUser->id == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa chính mình'
                ], 400);
            }
            
            DB::beginTransaction();
            
            if ($userToDelete->role === 'teacher') {
                // Xóa tất cả bài thi và câu hỏi của giáo viên
                $exams = $userToDelete->exams;
                foreach ($exams as $exam) {
                    // Xóa câu hỏi và đáp án
                    $exam->questions()->delete();
                    // Xóa bài thi
                    $exam->delete();
                }
                
                // Xóa lớp học (bảng classes)
                try {
                    $userToDelete->classrooms()->delete();
                } catch (\Exception $e) {
                    Log::warning('Cannot delete classrooms: ' . $e->getMessage());
                }
            }
            
            if ($userToDelete->role === 'student') {
                // Xóa kết quả thi
                $userToDelete->attempts()->delete();
            }
            
            $userName = $userToDelete->name;
            $userToDelete->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Đã xóa người dùng \"{$userName}\" và tất cả dữ liệu liên quan"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Force delete user error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Lấy danh sách học sinh
     */
    public function getStudents()
    {
        try {
            $students = User::where('role', 'student')
                ->select('id', 'name', 'email', 'student_code')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $students
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Lấy danh sách giáo viên
     */
    public function getTeachers()
    {
        try {
            $teachers = User::where('role', 'teacher')
                ->select('id', 'name', 'email')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $teachers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}