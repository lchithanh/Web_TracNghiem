<?php
// app/Http/Controllers/Api/AttemptController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttemptController extends Controller
{
    /**
     * Danh sách bài làm
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = Attempt::where('user_id', $user->id)->with(['exam.subject']);
            
            if ($request->has('exam_id') && $user->role === 'teacher') {
                $query->where('exam_id', $request->exam_id);
            }
            
            return response()->json([
                'success' => true,
                'data' => $query->orderByDesc('created_at')->get()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bắt đầu làm bài
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $exam = Exam::findOrFail($request->exam_id);
            
            Log::info('Attempt store', [
                'user_id' => $user->id,
                'exam_id' => $request->exam_id,
                'exam_title' => $exam->title
            ]);
            
            if ($user->role === 'student') {
                if ($exam->status !== 'published') {
                    return response()->json(['success' => false, 'message' => 'Bài thi chưa được mở'], 403);
                }
                
                $now = now();
                if ($exam->start_time && $now < $exam->start_time) {
                    return response()->json(['success' => false, 'message' => 'Bài thi chưa đến thời gian mở'], 403);
                }
                if ($exam->end_time && $now > $exam->end_time) {
                    return response()->json(['success' => false, 'message' => 'Bài thi đã đóng'], 403);
                }
                
                $maxAttempts = $exam->max_attempts ?? 1;
                $submittedCount = Attempt::where('user_id', $user->id)
                    ->where('exam_id', $exam->id)
                    ->where('status', 'submitted')
                    ->count();
                    
                if ($submittedCount >= $maxAttempts) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bạn đã hết số lần làm bài ({$submittedCount}/{$maxAttempts})"
                    ], 403);
                }
            }
            
            $attempt = Attempt::where('user_id', $user->id)
                ->where('exam_id', $exam->id)
                ->where('status', 'doing')
                ->first();
                
            if (!$attempt) {
                $attempt = Attempt::create([
                    'user_id' => $user->id,
                    'exam_id' => $exam->id,
                    'started_at' => now(),
                    'status' => 'doing',
                ]);
                Log::info('New attempt created', ['attempt_id' => $attempt->id]);
            } else {
                Log::info('Existing attempt found', ['attempt_id' => $attempt->id]);
            }
            
            return response()->json(['success' => true, 'data' => $attempt], 201);
            
        } catch (\Exception $e) {
            Log::error('Attempt store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chi tiết bài làm
     */
    public function show($id)
    {
        try {
            $attempt = Attempt::with(['exam.questions.answers', 'answers.answer'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);
            
            return response()->json(['success' => true, 'data' => $attempt]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài làm'
            ], 404);
        }
    }

    /**
     * Kết quả chi tiết
     */
    public function result($id)
    {
        try {
            $user = Auth::user();
            $attempt = Attempt::with(['exam.questions.answers', 'answers.answer', 'exam.subject'])
                ->where('user_id', $user->id)
                ->findOrFail($id);
            
            $total = $attempt->exam->questions->count();
            $correct = 0;
            $questions = [];
            
            foreach ($attempt->exam->questions as $q) {
                $answer = $attempt->answers->where('question_id', $q->id)->first();
                $isCorrect = $answer && $answer->answer && $answer->answer->is_correct;
                if ($isCorrect) $correct++;
                
                $questions[] = [
                    'id' => $q->id,
                    'content' => $q->content,
                    'user_answer' => $answer?->answer?->content,
                    'correct_answer' => $q->answers->where('is_correct', true)->first()?->content,
                    'is_correct' => $isCorrect
                ];
            }
            
            $attemptCount = Attempt::where('user_id', $user->id)
                ->where('exam_id', $attempt->exam_id)
                ->where('status', 'submitted')
                ->count();
            
            return response()->json(['success' => true, 'data' => [
                'id' => $attempt->id,
                'exam_id' => $attempt->exam_id,
                'exam_title' => $attempt->exam->title,
                'subject_name' => $attempt->exam->subject->name,
                'total_questions' => $total,
                'correct_answers' => $correct,
                'score' => round(($correct / max($total, 1)) * 10, 2),
                'attempt_number' => $attemptCount,
                'max_attempts' => $attempt->exam->max_attempts ?? 1,
                'time_spent' => $attempt->time_spent,
                'submitted_at' => $attempt->submitted_at,
                'questions' => $questions
            ]]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Nộp bài
     */
    public function submit(Request $request, $id)
    {
        try {
            $attempt = Attempt::where('user_id', Auth::id())
                ->where('status', 'doing')
                ->findOrFail($id);
            
            $total = $attempt->exam->questions->count();
            $correct = 0;
            foreach ($attempt->answers as $a) {
                if ($a->answer && $a->answer->is_correct) $correct++;
            }
            
            $score = $total > 0 ? ($correct / $total) * 10 : 0;
            
            DB::beginTransaction();
            $attempt->update([
                'score' => $score,
                'submitted_at' => now(),
                'status' => 'submitted',
                'time_spent' => $request->time_spent ?? now()->diffInSeconds($attempt->started_at)
            ]);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Nộp bài thành công',
                'data' => $attempt
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
     * Hủy bài làm
     */
    public function destroy($id)
    {
        try {
            $attempt = Attempt::where('user_id', Auth::id())
                ->where('status', 'doing')
                ->findOrFail($id);
            
            $attempt->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Hủy bài làm thành công'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy bài làm theo bài thi (cho giáo viên)
     * GET /api/attempts/exam/{examId}
     */
    public function getByExam($examId)
    {
        try {
            $user = Auth::user();
            
            $exam = Exam::findOrFail($examId);
            $subject = $exam->subject;
            
            if ($user->role === 'teacher' && $subject->created_by != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền xem bài làm của bài thi này'
                ], 403);
            }
            
            $attempts = Attempt::where('exam_id', $examId)
                ->with('user')
                ->where('status', 'submitted')
                ->orderByDesc('submitted_at')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $attempts
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy bài làm theo học sinh
     * GET /api/attempts/user/{userId}
     */
    public function getByUser($userId)
    {
        try {
            $user = Auth::user();
            
            if ($user->role === 'student' && $user->id != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền xem bài làm của người khác'
                ], 403);
            }
            
            $attempts = Attempt::where('user_id', $userId)
                ->with(['exam', 'exam.subject'])
                ->orderByDesc('created_at')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $attempts
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}