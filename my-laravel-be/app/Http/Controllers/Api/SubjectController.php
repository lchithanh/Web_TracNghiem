<?php
// app/Http/Controllers/Api/SubjectController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            
            if ($user && $user->role === 'student') {
                $subjects = Subject::whereHas('exams', function($q) {
                    $q->where('status', 'published');
                })->get();
            } 
            elseif ($user && $user->role === 'teacher') {
                $subjects = Subject::where('created_by', $user->id)->get();
            }
            elseif ($user && $user->role === 'admin') {
                $subjects = Subject::all();
            }
            else {
                $subjects = collect();
            }
            
            return response()->json([
                'success' => true,
                'data' => $subjects
            ]);
            
        } catch (\Exception $e) {
            Log::error('Subject index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách môn học: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            Log::info('Creating subject', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'name' => $request->name
            ]);
            
            if (!$user || !in_array($user->role, ['teacher', 'admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền tạo môn học'
                ], 403);
            }
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
            
            // Kiểm tra trùng tên môn học với cùng giáo viên
            $exists = Subject::where('name', $validated['name'])
                ->where('created_by', $user->id)
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã có môn học với tên này rồi'
                ], 400);
            }
            
            $subject = Subject::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'created_by' => $user->id,
            ]);
            
            Log::info('Subject created successfully', ['subject_id' => $subject->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Thêm môn học thành công',
                'data' => $subject
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Create subject error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo môn học: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::user();
            $subject = Subject::with('exams')->findOrFail($id);
            
            if ($user->role === 'teacher' && $subject->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem môn học này'
                ], 403);
            }
            
            if ($user->role === 'student') {
                $subject->exams = $subject->exams->filter(function($exam) {
                    return $exam->status === 'published';
                });
            }
            
            return response()->json([
                'success' => true,
                'data' => $subject
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy môn học'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            if (!$user || !in_array($user->role, ['teacher', 'admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật môn học'
                ], 403);
            }
            
            $subject = Subject::findOrFail($id);
            
            if ($user->role === 'teacher' && $subject->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền sửa môn học này'
                ], 403);
            }
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
            ]);
            
            // Kiểm tra trùng tên khi đổi tên
            if (isset($validated['name']) && $validated['name'] !== $subject->name) {
                $exists = Subject::where('name', $validated['name'])
                    ->where('created_by', $user->id)
                    ->where('id', '!=', $id)
                    ->exists();
                    
                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã có môn học với tên này rồi'
                    ], 400);
                }
            }
            
            $subject->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật môn học thành công',
                'data' => $subject
            ]);
            
        } catch (\Exception $e) {
            Log::error('Update subject error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật môn học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa môn học - Admin và giáo viên đều có quyền xóa môn của mình
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            if (!$user || !in_array($user->role, ['teacher', 'admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa môn học'
                ], 403);
            }
            
            $subject = Subject::findOrFail($id);
            
            if ($user->role !== 'admin' && $subject->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa môn học này'
                ], 403);
            }
            
            $examCount = $subject->exams()->count();
            
            if ($examCount > 0) {
                $exams = $subject->exams()->select('id', 'title')->get();
                
                return response()->json([
                    'success' => false,
                    'message' => "Không thể xóa môn học \"{$subject->name}\" vì đang có {$examCount} bài thi sử dụng.\nVui lòng xóa các bài thi trước.",
                    'exam_count' => $examCount,
                    'exams' => $exams,
                    'related_items' => [
                        'type' => 'exams',
                        'count' => $examCount,
                        'items' => $exams->toArray()
                    ]
                ], 400);
            }
            
            $subject->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa môn học thành công'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete subject error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa môn học: ' . $e->getMessage()
            ], 500);
        }
    }
}