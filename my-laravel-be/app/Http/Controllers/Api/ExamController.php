<?php
// app/Http/Controllers/Api/ExamController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamController extends Controller
{
    /**
     * Danh sách bài thi
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Exam::with(['subject', 'creator']);
            
            if ($request->has('subject_id')) $query->where('subject_id', $request->subject_id);
            if ($request->has('status')) $query->where('status', $request->status);
            
            if ($user->role === 'student') {
                $query->where('status', 'published');
                $myClasses = DB::table('class_students')->where('user_id', $user->id)->pluck('class_id');
                $examIds = DB::table('exam_class')->whereIn('class_id', $myClasses)->pluck('exam_id');
                if ($examIds->isNotEmpty()) {
                    $query->whereIn('id', $examIds);
                } else {
                    return response()->json(['success' => true, 'data' => []]);
                }
            } elseif ($user->role === 'teacher') {
                $query->whereHas('subject', fn($q) => $q->where('created_by', $user->id));
            }
            
            $exams = $query->orderByDesc('created_at')->get();
            
            if ($user->role === 'student') {
                $allAttempts = Attempt::where('user_id', $user->id)
                    ->where('status', 'submitted')
                    ->get()
                    ->groupBy('exam_id');
                
                foreach ($exams as $exam) {
                    $examAttempts = $allAttempts->get($exam->id, collect());
                    $latestAttempt = $examAttempts->sortByDesc('submitted_at')->first();
                    
                    $exam->has_submitted = $examAttempts->isNotEmpty();
                    $exam->attempt_count = $examAttempts->count();
                    $exam->max_attempts = $exam->max_attempts ?? 1;
                    $exam->remaining_attempts = ($exam->max_attempts ?? 1) - $examAttempts->count();
                    $exam->can_take = $exam->status === 'published' && $exam->remaining_attempts > 0;
                    $exam->attempt_id = $latestAttempt?->id;
                    $exam->score = $latestAttempt?->score;
                    $exam->completed = $examAttempts->isNotEmpty();
                }
            }
            
            return response()->json(['success' => true, 'data' => $exams]);
            
        } catch (\Exception $e) {
            Log::error('Exam index error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi server'], 500);
        }
    }

    /**
     * Chi tiết bài thi
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            $exam = Exam::with(['subject', 'questions.answers', 'creator'])->findOrFail($id);
            
            if ($user->role === 'teacher') {
                $subject = Subject::find($exam->subject_id);
                if (!$subject || $subject->created_by !== $user->id) {
                    return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
                }
            }
            
            if ($user->role === 'student') {
                if ($exam->status !== 'published') {
                    return response()->json(['success' => false, 'message' => 'Bài thi chưa mở'], 403);
                }
                
                $now = now();
                if ($exam->start_time && $now < $exam->start_time) {
                    return response()->json(['success' => false, 'message' => 'Chưa đến thời gian mở'], 403);
                }
                if ($exam->end_time && $now > $exam->end_time) {
                    return response()->json(['success' => false, 'message' => 'Bài thi đã đóng'], 403);
                }
                
                $classIds = DB::table('exam_class')->where('exam_id', $exam->id)->pluck('class_id');
                if ($classIds->isEmpty()) {
                    return response()->json(['success' => false, 'message' => 'Chưa giao lớp'], 403);
                }
                
                $isEnrolled = DB::table('class_students')
                    ->whereIn('class_id', $classIds)
                    ->where('user_id', $user->id)
                    ->exists();
                    
                if (!$isEnrolled) {
                    return response()->json(['success' => false, 'message' => 'Chưa tham gia lớp'], 403);
                }
                
                $maxAttempts = $exam->max_attempts ?? 1;
                $submittedCount = Attempt::where('user_id', $user->id)
                    ->where('exam_id', $exam->id)
                    ->where('status', 'submitted')
                    ->count();
                
                $exam->attempt_count = $submittedCount;
                $exam->max_attempts = $maxAttempts;
                $exam->remaining_attempts = $maxAttempts - $submittedCount;
                $exam->can_take = $exam->remaining_attempts > 0;
                
                if ($submittedCount >= $maxAttempts) {
                    return response()->json([
                        'success' => false, 
                        'message' => "Hết số lần làm ({$submittedCount}/{$maxAttempts})",
                        'data' => $exam
                    ], 403);
                }
            }
            
            return response()->json(['success' => true, 'data' => $exam]);
            
        } catch (\Exception $e) {
            Log::error('Exam show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server'
            ], 500);
        }
    }

    /**
     * Tạo bài thi
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'teacher') {
                return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
            }
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'subject_id' => 'required|exists:subjects,id',
                'description' => 'nullable|string',
                'duration' => 'required|integer|min:1',
                'max_attempts' => 'sometimes|integer|min:1|max:10',
                'total_questions' => 'nullable|integer|min:0',
                'status' => 'required|in:draft,published,closed',
                'start_time' => 'nullable|date',
                'end_time' => 'nullable|date|after:start_time',
            ]);
            
            $subject = Subject::find($validated['subject_id']);
            if (!$subject || $subject->created_by !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Không có quyền tạo bài cho môn này'], 403);
            }
            
            DB::beginTransaction();
            $exam = Exam::create([
                'title' => $validated['title'],
                'subject_id' => $validated['subject_id'],
                'description' => $validated['description'] ?? null,
                'duration' => $validated['duration'],
                'max_attempts' => $validated['max_attempts'] ?? 1,
                'total_questions' => $validated['total_questions'] ?? 0,
                'status' => $validated['status'],
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'created_by' => $user->id,
            ]);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Tạo bài thi thành công',
                'data' => $exam->load('subject')
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật bài thi
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'teacher') {
                return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
            }
            
            $exam = Exam::findOrFail($id);
            $subject = Subject::find($exam->subject_id);
            if (!$subject || $subject->created_by !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Không có quyền sửa'], 403);
            }
            
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'duration' => 'sometimes|integer|min:1',
                'max_attempts' => 'sometimes|integer|min:1|max:10',
                'status' => 'sometimes|in:draft,published,closed',
                'start_time' => 'nullable|date',
                'end_time' => 'nullable|date|after:start_time',
            ]);
            
            DB::beginTransaction();
            $exam->update($validated);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công',
                'data' => $exam->fresh('subject')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa bài thi - Cho phép xóa kèm dữ liệu liên quan
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            if (!$user || !in_array($user->role, ['admin', 'teacher'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa bài thi'
                ], 403);
            }
            
            $exam = Exam::find($id);
            if (!$exam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài thi'
                ], 404);
            }
            
            $subject = Subject::find($exam->subject_id);
            if ($user->role !== 'admin' && (!$subject || $subject->created_by !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa bài thi này'
                ], 403);
            }
            
            $questionCount = $exam->questions()->count();
            $attemptCount = $exam->attempts()->count();
            
            // Kiểm tra tham số force từ request
            $force = $request->input('force', false);
            
            // Nếu có dữ liệu liên quan và không phải force delete
            if (($questionCount > 0 || $attemptCount > 0) && !$force) {
                $warnings = [];
                if ($questionCount > 0) {
                    $warnings[] = "📝 Đang có {$questionCount} câu hỏi";
                }
                if ($attemptCount > 0) {
                    $warnings[] = "👨‍🎓 Đã có {$attemptCount} lượt làm bài";
                }
                
                $warningMessage = "⚠️ CẢNH BÁO: Bài thi \"{$exam->title}\" có dữ liệu liên quan!\n\n";
                $warningMessage .= implode("\n", $warnings);
                $warningMessage .= "\n\n❓ Bạn có chắc chắn muốn xóa bài thi này không?\n";
                $warningMessage .= "⚠️ Hành động này sẽ xóa TẤT CẢ dữ liệu liên quan (câu hỏi, kết quả thi).\n";
                $warningMessage .= "💡 Khuyến nghị: Nên đóng bài thi thay vì xóa để giữ kết quả.\n\n";
                $warningMessage .= "🔴 Để xóa, vui lòng gửi request với tham số force=true";
                
                return response()->json([
                    'success' => false,
                    'message' => $warningMessage,
                    'warnings' => $warnings,
                    'question_count' => $questionCount,
                    'attempt_count' => $attemptCount,
                    'requires_confirmation' => true,
                    'force_required' => true
                ], 400);
            }
            
            // Tiến hành xóa với force = true
            DB::beginTransaction();
            
            // Xóa câu hỏi và đáp án
            $deletedQuestions = 0;
            if ($questionCount > 0) {
                $deletedQuestions = $exam->questions()->delete();
            }
            
            // Xóa lượt làm bài và câu trả lời chi tiết
            $deletedAttempts = 0;
            if ($attemptCount > 0) {
                $deletedAttempts = $exam->attempts()->delete();
            }
            
            // Xóa liên kết với lớp
            DB::table('exam_class')->where('exam_id', $exam->id)->delete();
            
            // Xóa bài thi
            $examTitle = $exam->title;
            $exam->delete();
            
            DB::commit();
            
            $message = "✅ Đã xóa bài thi \"{$examTitle}\"";
            if ($deletedQuestions > 0) {
                $message .= " và {$deletedQuestions} câu hỏi";
            }
            if ($deletedAttempts > 0) {
                $message .= " và {$deletedAttempts} lượt làm bài";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted' => [
                    'exam' => 1,
                    'questions' => $deletedQuestions,
                    'attempts' => $deletedAttempts
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete exam error: ' . $e->getMessage());
            Log::error('Delete exam trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Xóa tất cả câu hỏi của bài thi
     */
    public function deleteAllQuestions($id)
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['admin', 'teacher'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            $exam = Exam::findOrFail($id);
            $subject = Subject::find($exam->subject_id);
            
            if ($user->role !== 'admin' && (!$subject || $subject->created_by !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền xóa câu hỏi của bài thi này'
                ], 403);
            }
            
            $questionCount = $exam->questions()->count();
            
            DB::beginTransaction();
            $exam->questions()->delete();
            $exam->total_questions = 0;
            $exam->save();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$questionCount} câu hỏi của bài thi \"{$exam->title}\""
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'teacher') {
                return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
            }
            
            $exam = Exam::findOrFail($id);
            $subject = Subject::find($exam->subject_id);
            if (!$subject || $subject->created_by !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
            }
            
            $validated = $request->validate(['status' => 'required|in:draft,published,closed']);
            
            if ($validated['status'] === 'published' && $exam->questions()->count() === 0) {
                return response()->json(['success' => false, 'message' => 'Chưa có câu hỏi, không thể xuất bản'], 400);
            }
            
            DB::beginTransaction();
            $exam->status = $validated['status'];
            $exam->save();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $exam
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Lấy bài thi theo môn
     */
    public function getBySubject($subjectId)
    {
        try {
            $exams = Exam::where('subject_id', $subjectId);
            if (Auth::user()->role === 'student') {
                $exams->where('status', 'published');
            }
            return response()->json([
                'success' => true,
                'data' => $exams->orderByDesc('created_at')->get()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Lấy bài thi theo lớp
     */
    public function getByClass($classId)
    {
        try {
            $user = Auth::user();
            
            $class = Classroom::findOrFail($classId);
            
            if ($user->role === 'teacher' && $class->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền xem bài thi của lớp này'
                ], 403);
            }
            
            if ($user->role === 'student') {
                $isEnrolled = DB::table('class_students')
                    ->where('class_id', $classId)
                    ->where('user_id', $user->id)
                    ->exists();
                    
                if (!$isEnrolled) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không phải thành viên của lớp này'
                    ], 403);
                }
            }
            
            $exams = $class->exams()
                ->with(['subject', 'creator'])
                ->orderByDesc('created_at')
                ->get();
            
            if ($user->role === 'student') {
                $allAttempts = Attempt::where('user_id', $user->id)
                    ->where('status', 'submitted')
                    ->get()
                    ->groupBy('exam_id');
                
                foreach ($exams as $exam) {
                    $examAttempts = $allAttempts->get($exam->id, collect());
                    $latestAttempt = $examAttempts->sortByDesc('submitted_at')->first();
                    
                    $exam->has_submitted = $examAttempts->isNotEmpty();
                    $exam->attempt_count = $examAttempts->count();
                    $exam->max_attempts = $exam->max_attempts ?? 1;
                    $exam->remaining_attempts = ($exam->max_attempts ?? 1) - $examAttempts->count();
                    $exam->can_take = $exam->status === 'published' && $exam->remaining_attempts > 0;
                    $exam->attempt_id = $latestAttempt?->id;
                    $exam->score = $latestAttempt?->score;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $exams
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get class exams error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}