<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExamController extends Controller
{
    private function resolveLimit(Request $request, int $default = 0, int $max = 100)
    {
        $limit = (int) $request->input('limit', $default);
        if ($limit <= 0) return null;
        return min($limit, $max);
    }

    private function examListQuery()
    {
        return Exam::query()
            ->with([
                'subject:id,name,created_by',
                'creator:id,name,email,avatar',
            ])
            ->select([
                'id', 'subject_id', 'title', 'description', 'duration', 'max_attempts',
                'total_questions', 'created_by', 'status', 'start_time', 'end_time',
            ]);
    }

    private function canManageSubjectExams($user, Subject $subject): bool
    {
        return in_array($user->role, ['admin', 'teacher'], true);
    }

    private function canManageExam($user, Exam $exam): bool
    {
        return in_array($user->role, ['admin', 'teacher'], true);
    }

    private function appendStudentAttemptStats($exams, int $userId)
    {
        $attemptsByExam = Attempt::query()
            ->where('user_id', $userId)
            ->where('status', 'submitted')
            ->select('id', 'exam_id', 'score', 'submitted_at')
            ->get()
            ->groupBy('exam_id');

        foreach ($exams as $exam) {
            $examAttempts  = $attemptsByExam->get($exam->id, collect())->sortByDesc('submitted_at')->values();
            $latestAttempt = $examAttempts->first();
            $maxAttempts   = $exam->max_attempts ?? 1;
            $attemptCount  = $examAttempts->count();

            $exam->has_submitted      = $attemptCount > 0;
            $exam->attempt_count      = $attemptCount;
            $exam->max_attempts       = $maxAttempts;
            $exam->remaining_attempts = max($maxAttempts - $attemptCount, 0);
            $exam->can_take           = $exam->status === 'published' && $exam->remaining_attempts > 0;
            $exam->attempt_id         = $latestAttempt?->id;
            $exam->score              = $latestAttempt?->score;
            $exam->completed          = $attemptCount > 0;
        }

        return $exams;
    }

    public function index(Request $request)
{
    try {
        $user  = Auth::user();
        $limit = $this->resolveLimit($request);
        $query = $this->examListQuery();

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->integer('subject_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($user->role === 'student') {
            $query->where('status', 'published')
                ->whereExists(function ($subQuery) use ($user) {
                    $subQuery->select(DB::raw(1))
                        ->from('exam_class')
                        ->join('class_students', 'class_students.class_id', '=', 'exam_class.class_id')
                        ->whereColumn('exam_class.exam_id', 'exams.id')
                        ->where('class_students.user_id', $user->id);
                });
        }

        if ($user->role === 'teacher') {
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('teachers', fn($tq) => $tq->where('users.id', $user->id));
            });
        }

        $query->orderByDesc('id');
        $exams = $limit ? $query->limit($limit)->get() : $query->get();

        if ($user->role === 'student') {
            $exams = $this->appendStudentAttemptStats($exams, $user->id);
        }

        return response()->json(['success' => true, 'data' => $exams]);
    } catch (\Exception $e) {
        Log::error('Exam index error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Server error'], 500);
    }
}

    public function show($id)
    {
        try {
            $user = Auth::user();

            $exam = Exam::query()
                ->with([
                    'subject:id,name,created_by',
                    'creator:id,name,email,avatar',
                    'questions:id,exam_id,content,image,level',
                    'questions.answers:id,question_id,content,is_correct',
                ])
                ->findOrFail($id);

            if ($user->role === 'teacher' && !$this->canManageExam($user, $exam)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            if ($user->role === 'student') {
                if ($exam->status !== 'published') {
                    return response()->json(['success' => false, 'message' => 'Exam not published'], 403);
                }

                $now = now();
                try {
                    $start = $exam->start_time ? Carbon::parse($exam->start_time) : null;
                    $end   = $exam->end_time   ? Carbon::parse($exam->end_time)   : null;
                } catch (\Exception $e) {
                    Log::warning("Invalid exam times for exam {$exam->id}: " . $e->getMessage());
                    $start = $end = null;
                }

                if ($start && $now->lt($start)) {
                    return response()->json(['success' => false, 'message' => 'Exam not started'], 403);
                }
                if ($end && $now->gt($end)) {
                    return response()->json(['success' => false, 'message' => 'Exam closed'], 403);
                }

                try {
                    $isEnrolled = DB::table('exam_class')
                        ->join('class_students', 'class_students.class_id', '=', 'exam_class.class_id')
                        ->where('exam_class.exam_id', $exam->id)
                        ->where('class_students.user_id', $user->id)
                        ->exists();
                } catch (\Exception $e) {
                    Log::error("Enrollment check failed for user {$user->id}, exam {$exam->id}: " . $e->getMessage());
                    $isEnrolled = false;
                }

                if (!$isEnrolled) {
                    return response()->json(['success' => false, 'message' => 'Not in class'], 403);
                }

                $maxAttempts = $exam->max_attempts ?? 1;
                try {
                    $submittedCount = Attempt::query()
                        ->where('user_id', $user->id)
                        ->where('exam_id', $exam->id)
                        ->where('status', 'submitted')
                        ->count();
                } catch (\Exception $e) {
                    Log::error("Attempt count failed for user {$user->id}, exam {$exam->id}: " . $e->getMessage());
                    $submittedCount = 0;
                }

                $exam->attempt_count      = $submittedCount;
                $exam->max_attempts       = $maxAttempts;
                $exam->remaining_attempts = max($maxAttempts - $submittedCount, 0);
                $exam->can_take           = $exam->remaining_attempts > 0;

                if ($submittedCount >= $maxAttempts) {
                    return response()->json([
                        'success' => false,
                        'message' => "No attempts left ({$submittedCount}/{$maxAttempts})",
                        'data'    => $exam,
                    ], 403);
                }
            }

            return response()->json(['success' => true, 'data' => $exam]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Exam not found'], 404);
        } catch (\Exception $e) {
            Log::error('Exam show error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!in_array($user->role, ['admin', 'teacher'], true)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            $validated = $request->validate([
                'title'           => 'required|string|max:255',
                'subject_id'      => 'required|exists:subjects,id',
                'description'     => 'nullable|string',
                'duration'        => 'required|integer|min:1',
                'max_attempts'    => 'sometimes|integer|min:1|max:10',
                'total_questions' => 'nullable|integer|min:0',
                'status'          => 'required|in:draft,published,closed',
                'start_time'      => 'nullable|date',
                'end_time'        => 'nullable|date|after:start_time',
            ]);

            $subject = Subject::find($validated['subject_id']);
            if (!$subject || !$this->canManageSubjectExams($user, $subject)) {
                return response()->json(['success' => false, 'message' => 'Forbidden for this subject'], 403);
            }

            DB::beginTransaction();
            $exam = Exam::create([
                'title'           => $validated['title'],
                'subject_id'      => $validated['subject_id'],
                'description'     => $validated['description'] ?? null,
                'duration'        => $validated['duration'],
                'max_attempts'    => $validated['max_attempts'] ?? 1,
                'total_questions' => $validated['total_questions'] ?? 0,
                'status'          => $validated['status'],
                'start_time'      => $validated['start_time'] ?? null,
                'end_time'        => $validated['end_time'] ?? null,
                'created_by'      => $user->id,
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Exam created',
                'data'    => $exam->load('subject:id,name,created_by'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exam store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!in_array($user->role, ['admin', 'teacher'], true)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            $exam = Exam::find($id);
            if (!$exam || !$this->canManageExam($user, $exam)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            $validated = $request->validate([
                'title'        => 'sometimes|string|max:255',
                'description'  => 'nullable|string',
                'duration'     => 'sometimes|integer|min:1',
                'max_attempts' => 'sometimes|integer|min:1|max:10',
                'status'       => 'sometimes|in:draft,published,closed',
                'start_time'   => 'nullable|date',
                'end_time'     => 'nullable|date|after:start_time',
            ]);

            DB::beginTransaction();
            $exam->update($validated);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Exam updated',
                'data'    => $exam->fresh('subject:id,name,created_by'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exam update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ✅ ĐÃ SỬA: Thay exam_questions → $exam->questions()
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || !in_array($user->role, ['admin', 'teacher'], true)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            $exam = Exam::find($id);
            if (!$exam) {
                return response()->json(['success' => false, 'message' => 'Exam not found'], 404);
            }

            if (!$this->canManageExam($user, $exam)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            // ✅ Dùng relationship thay vì exam_questions
            $questionCount = $exam->questions()->count();
            $attemptCount  = $exam->attempts()->count();
            $force         = $request->boolean('force');

            if (($questionCount > 0 || $attemptCount > 0) && !$force) {
                $warnings = [];
                if ($questionCount > 0) $warnings[] = "Đang có {$questionCount} câu hỏi";
                if ($attemptCount > 0)  $warnings[] = "Đã có {$attemptCount} lượt làm bài";

                return response()->json([
                    'success'               => false,
                    'message'               => "Exam '{$exam->title}' has related data. Send force=true to delete.",
                    'warnings'              => $warnings,
                    'question_count'        => $questionCount,
                    'attempt_count'         => $attemptCount,
                    'requires_confirmation' => true,
                    'force_required'        => true,
                ], 400);
            }

            DB::beginTransaction();

            // ✅ Lấy id câu hỏi qua relationship
            $questionIds = $exam->questions()->pluck('id')->all();

            // ✅ Xóa answers trước
            if (!empty($questionIds)) {
                DB::table('answers')->whereIn('question_id', $questionIds)->delete();
            }

            // ✅ Xóa questions, attempts, exam_class
            $exam->questions()->delete();
            $deletedAttempts = $exam->attempts()->delete();
            DB::table('exam_class')->where('exam_id', $exam->id)->delete();

            $title = $exam->title;
            $exam->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Deleted exam '{$title}'",
                'deleted' => [
                    'exam'      => 1,
                    'questions' => count($questionIds),
                    'attempts'  => $deletedAttempts,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete exam error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ✅ ĐÃ SỬA: Thay exam_questions → $exam->questions()
     */
    public function deleteAllQuestions($id)
    {
        try {
            $user = Auth::user();
            if (!in_array($user->role, ['admin', 'teacher'], true)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            $exam = Exam::findOrFail($id);
            if (!$this->canManageExam($user, $exam)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            DB::beginTransaction();

            // ✅ Lấy id qua relationship
            $questionIds = $exam->questions()->pluck('id')->all();

            if (!empty($questionIds)) {
                DB::table('answers')->whereIn('question_id', $questionIds)->delete();
            }

            $exam->questions()->delete();
            $exam->update(['total_questions' => 0]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Deleted all questions in exam']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('deleteAllQuestions error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!in_array($user->role, ['admin', 'teacher'], true)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            $exam = Exam::find($id);
            if (!$exam || !$this->canManageExam($user, $exam)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            $validated = $request->validate(['status' => 'required|in:draft,published,closed']);

            if ($validated['status'] === 'published' && !$exam->questions()->exists()) {
                return response()->json(['success' => false, 'message' => 'Không thể xuất bản nếu không có câu hỏi!'], 400);
            }

            DB::beginTransaction();
            $exam->update(['status' => $validated['status']]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status updated',
                'data'    => $exam,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exam updateStatus error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}