<?php
// app/Http/Controllers/Api/SubjectController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    // ==================== LIST SUBJECTS ====================
    public function index()
    {
        try {
            $user = Auth::user();

            if ($user->role === 'student') {
                $subjects = Subject::whereHas('exams', function($q) {
                    $q->where('status', 'published');
                })->get();
            } elseif ($user->role === 'teacher') {
                $subjects = Subject::whereHas('teachers', function($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                })->get();
            } elseif ($user->role === 'admin') {
                $subjects = Subject::all();
            } else {
                $subjects = collect();
            }

            return response()->json(['success' => true, 'data' => $subjects]);

        } catch (\Exception $e) {
            Log::error('Subject index error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi khi lấy danh sách môn học'], 500);
        }
    }

    // ==================== CREATE SUBJECT ====================
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền tạo môn học'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subject = Subject::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by' => $user->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Tạo môn học thành công', 'data' => $subject], 201);
    }

    // ==================== SHOW SUBJECT ====================
    public function show($id)
    {
        try {
            $user = Auth::user();
            $subject = Subject::with('exams', 'teachers')->findOrFail($id);

            if ($user->role === 'teacher' && !$subject->teachers->contains($user->id)) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền xem môn học này'], 403);
            }

            if ($user->role === 'student') {
                $subject->exams = $subject->exams->filter(fn($exam) => $exam->status === 'published');
            }

            return response()->json(['success' => true, 'data' => $subject]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy môn học'], 404);
        }
    }

    // ==================== UPDATE SUBJECT ====================
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền cập nhật môn học'], 403);
        }

        try {
            $subject = Subject::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $subject->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? $subject->description,
            ]);

            return response()->json(['success' => true, 'message' => 'Cập nhật môn học thành công', 'data' => $subject]);

        } catch (\Exception $e) {
            Log::error("Subject update error: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Lỗi khi cập nhật môn học'], 500);
        }
    }

    // ==================== DELETE SUBJECT ====================
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa môn học'], 403);
        }

        try {
            $subject = Subject::findOrFail($id);

            if ($subject->exams()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Không thể xóa môn học \"{$subject->name}\" vì đang có bài thi.",
                ], 400);
            }

            $subject->delete();
            return response()->json(['success' => true, 'message' => 'Xóa môn học thành công']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi khi xóa môn học'], 500);
        }
    }

    // ==================== TEACHER ASSIGNMENT ====================
    public function getTeachers($subjectId)
    {
        $subject = Subject::find($subjectId);
        if (!$subject) return response()->json(['success' => false, 'message' => 'Subject not found'], 404);

        return response()->json(['success' => true, 'data' => $subject->teachers]);
    }

    public function getAvailableTeachers($subjectId)
    {
        $subject = Subject::find($subjectId);
        if (!$subject) return response()->json(['success' => false, 'message' => 'Subject not found'], 404);

        $assignedIds = $subject->teachers->pluck('id')->toArray();
        $available = User::where('role', 'teacher')->whereNotIn('id', $assignedIds)->get();

        return response()->json(['success' => true, 'data' => $available]);
    }

    public function assignTeacher(Request $request, $subjectId)
    {
        $teacherIds = $request->input('teacher_ids', []);
        $subject = Subject::find($subjectId);
        if (!$subject) return response()->json(['success' => false, 'message' => 'Subject not found'], 404);

        $validIds = User::whereIn('id', $teacherIds)->where('role', 'teacher')->pluck('id')->toArray();
        $subject->teachers()->syncWithoutDetaching($validIds);

        return response()->json(['success' => true, 'message' => 'Phân công giáo viên thành công', 'assigned_teacher_ids' => $validIds]);
    }

    public function unassignTeacher($subjectId, $teacherId)
    {
        $subject = Subject::find($subjectId);
        if (!$subject) return response()->json(['success' => false, 'message' => 'Subject not found'], 404);

        $user = User::find($teacherId);
        if (!$user || $user->role !== 'teacher') return response()->json(['success' => false, 'message' => 'ID này không phải giáo viên'], 400);

        $subject->teachers()->detach($teacherId);
        return response()->json(['success' => true, 'message' => 'Bỏ phân công giáo viên thành công']);
    }
}