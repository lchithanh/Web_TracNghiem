<?php
// app/Http/Controllers/Api/ClassroomController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    /**
     * Danh sách lớp (theo role)
     */
   public function index()
    {
        try {
            $user = Auth::user();
            
            if ($user->role === 'teacher') {
                $classrooms = Classroom::with(['teacher', 'students', 'exams'])  // THÊM 'exams'
                    ->where('teacher_id', $user->id)
                    ->get();
            } elseif ($user->role === 'student') {
                $classrooms = $user->enrolledClasses()
                    ->with(['teacher', 'students', 'exams'])  // THÊM 'exams'
                    ->get();
            } else {
                $classrooms = Classroom::with(['teacher', 'students', 'exams'])  // THÊM 'exams'
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $classrooms
            ]);
            
        } catch (\Exception $e) {
            Log::error('Classroom index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách lớp học'
            ], 500);
        }
    }

    /**
     * Chi tiết lớp - Load cả exams
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            $classroom = Classroom::with(['teacher', 'students', 'exams'])  // THÊM 'exams'
                ->findOrFail($id);
            
            if ($user->role === 'teacher' && $classroom->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem lớp học này'
                ], 403);
            }
            
            if ($user->role === 'student') {
                $isEnrolled = $classroom->students()->where('user_id', $user->id)->exists();
                    
                if (!$isEnrolled) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không phải là thành viên của lớp này'
                    ], 403);
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $classroom
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lớp học'
            ], 404);
        }
    }
    /**
     * Tạo lớp mới
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền tạo lớp học'
                ], 403);
            }
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
            
            $classroom = Classroom::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'teacher_id' => $user->id,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Tạo lớp học thành công',
                'data' => $classroom->load('teacher')
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Classroom store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo lớp học: ' . $e->getMessage()
            ], 500);
        }
    }

    

    /**
     * Cập nhật lớp
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $classroom = Classroom::findOrFail($id);
            
            if ($user->role !== 'admin' && ($user->role !== 'teacher' || $classroom->teacher_id !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền sửa lớp học này'
                ], 403);
            }
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
            ]);
            
            $classroom->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật lớp học thành công',
                'data' => $classroom
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật lớp học'
            ], 500);
        }
    }

    /**
     * Xóa lớp học (giải tán lớp) - Xóa tất cả dữ liệu liên quan
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $classroom = Classroom::findOrFail($id);
            
            // Kiểm tra quyền
            if ($user->role !== 'admin' && ($user->role !== 'teacher' || $classroom->teacher_id !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa lớp học này'
                ], 403);
            }
            
            $className = $classroom->name;
            $studentCount = $classroom->students()->count();
            $examCount = $classroom->exams()->count();
            
            DB::beginTransaction();
            
            // Xóa liên kết học sinh
            $classroom->students()->detach();
            
            // Xóa liên kết bài thi
            $classroom->exams()->detach();
            
            // Xóa lớp
            $classroom->delete();
            
            DB::commit();
            
            $message = "Đã giải tán lớp \"{$className}\"";
            if ($studentCount > 0) $message .= ", {$studentCount} học sinh đã bị xóa khỏi lớp";
            if ($examCount > 0) $message .= ", {$examCount} bài thi đã được gỡ";
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_students' => $studentCount,
                'deleted_exams' => $examCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete classroom error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa lớp học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy mã mời của lớp
     */
    public function getInviteCode($id)
    {
        try {
            $user = Auth::user();
            $classroom = Classroom::findOrFail($id);
            
            if ($user->role !== 'admin' && ($user->role !== 'teacher' || $classroom->teacher_id !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'invite_code' => $classroom->invite_code,
                    'expires_at' => $classroom->invite_expires_at,
                    'is_valid' => $classroom->isInviteValid()
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
     * Tạo lại mã mời
     */
    public function regenerateInviteCode($id)
    {
        try {
            $user = Auth::user();
            $classroom = Classroom::findOrFail($id);
            
            if ($user->role !== 'admin' && ($user->role !== 'teacher' || $classroom->teacher_id !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            $classroom->regenerateInviteCode();
            
            return response()->json([
                'success' => true,
                'message' => 'Tạo mã mới thành công',
                'data' => [
                    'invite_code' => $classroom->invite_code,
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
     * Giao bài thi cho lớp
     */
    public function assignExam(Request $request, $classId)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            $classroom = Classroom::findOrFail($classId);
            
            if ($classroom->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền giao bài cho lớp này'
                ], 403);
            }
            
            $validated = $request->validate([
                'exam_id' => 'required|exists:exams,id'
            ]);
            
            $exam = Exam::findOrFail($validated['exam_id']);
            
            $subject = $exam->subject;
            if ($subject->created_by != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền giao bài thi này'
                ], 403);
            }
            
            $alreadyAssigned = $classroom->exams()->where('exam_id', $exam->id)->exists();
            
            if ($alreadyAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài thi đã được giao cho lớp này'
                ], 400);
            }
            
            $classroom->exams()->attach($exam->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Giao bài thi thành công'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Assign exam error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách bài thi đã giao cho lớp
     */
    public function getClassExams($classId)
    {
        try {
            $user = Auth::user();
            $classroom = Classroom::findOrFail($classId);
            
            if ($user->role === 'teacher' && $classroom->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem bài thi của lớp này'
                ], 403);
            }
            
            if ($user->role === 'student') {
                $isEnrolled = $classroom->students()->where('user_id', $user->id)->exists();
                
                if (!$isEnrolled) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không phải là thành viên của lớp này'
                    ], 403);
                }
            }
            
            $exams = $classroom->exams()->with('subject')->get();
            
            return response()->json(['success' => true, 'data' => $exams]);
            
        } catch (\Exception $e) {
            Log::error('Get class exams error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa bài thi khỏi lớp
     */
    public function removeExam($classId, $examId)
    {
        try {
            $user = Auth::user();
            $classroom = Classroom::findOrFail($classId);
            
            if ($user->role !== 'admin' && ($user->role !== 'teacher' || $classroom->teacher_id !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            $classroom->exams()->detach($examId);
            
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa bài thi khỏi lớp'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}