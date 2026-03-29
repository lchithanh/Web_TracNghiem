<?php
// app/Http/Controllers/Api/HomeController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Dashboard data theo role
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }
        
        $data = match ($user->role) {
            'admin' => $this->getAdminData(),
            'teacher' => $this->getTeacherData($user),
            default => $this->getStudentData($user),
        };
        
        return response()->json([
            'success' => true,
            'role' => $user->role,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => $user->role,
            ],
            'data' => $data,
        ]);
    }
    
    /**
     * Dữ liệu cho học sinh
     */
    // app/Http/Controllers/Api/HomeController.php

private function getStudentData($user)
{
    // Lấy danh sách lớp học sinh đã tham gia
    $myClassIds = DB::table('class_students')
        ->where('user_id', $user->id)
        ->pluck('class_id')
        ->toArray();
    
    // Lấy danh sách bài thi được giao cho các lớp đó
    $examIds = DB::table('exam_class')
        ->whereIn('class_id', $myClassIds)
        ->pluck('exam_id')
        ->toArray();
    
    // Lấy bài thi có thể làm (published và chưa làm)
    $doneExamIds = Attempt::where('user_id', $user->id)
        ->where('status', 'submitted')
        ->pluck('exam_id')
        ->unique();
    
    $availableExams = Exam::with('subject:id,name')
        ->where('status', 'published')
        ->whereIn('id', $examIds)
        ->whereNotIn('id', $doneExamIds)
        ->get(['id', 'subject_id', 'title', 'description', 'duration', 'total_questions']);
    
    // Lấy danh sách môn học có bài thi published
    $subjects = Subject::whereHas('exams', function($q) use ($examIds) {
            $q->where('status', 'published')
              ->whereIn('id', $examIds);
        })
        ->withCount(['exams' => function($q) use ($examIds) {
            $q->where('status', 'published')
              ->whereIn('id', $examIds);
        }])
        ->get(['id', 'name', 'description']);
    
    // Lấy 5 lần làm bài gần nhất
    $recentAttempts = Attempt::with(['exam:id,title,subject_id', 'exam.subject:id,name'])
        ->where('user_id', $user->id)
        ->where('status', 'submitted')
        ->orderByDesc('submitted_at')
        ->take(5)
        ->get(['id', 'exam_id', 'score', 'status', 'submitted_at', 'time_spent']);
    
    // Thống kê
    $submittedAttempts = Attempt::where('user_id', $user->id)
        ->where('status', 'submitted')
        ->get(['score']);
    
    return [
        'subjects'        => $subjects,
        'recent_attempts' => $recentAttempts,
        'available_exams' => $availableExams,
        'stats' => [
            'total_attempts' => Attempt::where('user_id', $user->id)->count(),
            'completed'      => $submittedAttempts->count(),
            'avg_score'      => round($submittedAttempts->avg('score') ?? 0, 1),
            'best_score'     => $submittedAttempts->max('score') ?? 0,
        ],
    ];
}
    
    /**
     * Dữ liệu cho giáo viên
     */
    private function getTeacherData($user)
    {
        $exams = Exam::with('subject:id,name')
            ->where('created_by', $user->id)
            ->withCount('attempts')
            ->withAvg('attempts', 'score')
            ->orderByDesc('created_at')
            ->get(['id', 'subject_id', 'title', 'status', 'duration', 'total_questions', 'created_at']);
        
        $examIds = $exams->pluck('id');
        
        $totalAttempts = Attempt::whereIn('exam_id', $examIds)->count();
        $submittedAttempts = Attempt::whereIn('exam_id', $examIds)
            ->where('status', 'submitted')
            ->get();
        
        return [
            'exams' => $exams,
            'top_exams' => $exams->sortByDesc('attempts_count')->take(5)->values(),
            'stats' => [
                'total_exams' => $exams->count(),
                'published_exams' => $exams->where('status', 'published')->count(),
                'draft_exams' => $exams->where('status', 'draft')->count(),
                'closed_exams' => $exams->where('status', 'closed')->count(),
                'total_questions' => Question::whereIn('exam_id', $examIds)->count(),
                'total_attempts' => $totalAttempts,
                'completed_attempts' => $submittedAttempts->count(),
                'avg_score' => round($submittedAttempts->avg('score') ?? 0, 1),
            ],
        ];
    }
    
    /**
     * Dữ liệu cho admin
     */
    private function getAdminData()
    {
        $subjectStats = Subject::withCount('exams')
            ->get(['id', 'name'])
            ->map(function ($subject) {
                $examIds = Exam::where('subject_id', $subject->id)->pluck('id');
                
                $attemptQuery = Attempt::whereIn('exam_id', $examIds);
                
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'exam_count' => $subject->exams_count,
                    'total_attempts' => $attemptQuery->count(),
                    'avg_score' => round($attemptQuery->avg('score') ?? 0, 1),
                ];
            });
        
        $recentUsers = User::orderByDesc('created_at')
            ->take(10)
            ->get(['id', 'name', 'email', 'role', 'created_at']);
        
        $recentAttempts = Attempt::with([
                'user:id,name,email',
                'exam:id,title,subject_id',
                'exam.subject:id,name',
            ])
            ->where('status', 'submitted')
            ->orderByDesc('submitted_at')
            ->take(10)
            ->get(['id', 'user_id', 'exam_id', 'score', 'status', 'submitted_at']);
        
        return [
            'stats' => [
                'total_users' => User::count(),
                'total_students' => User::where('role', 'student')->count(),
                'total_teachers' => User::where('role', 'teacher')->count(),
                'total_exams' => Exam::count(),
                'total_attempts' => Attempt::count(),
                'total_subjects' => Subject::count(),
            ],
            'subject_stats' => $subjectStats,
            'recent_users' => $recentUsers,
            'recent_attempts' => $recentAttempts,
        ];
    }
}