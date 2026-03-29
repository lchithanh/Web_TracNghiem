<?php
// routes/api.php

use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\JoinController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\ClassStudentController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\AttemptController;
use App\Http\Controllers\Api\AttemptAnswerController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\StudentResultController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (không cần xác thực)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public routes for checking invite code
Route::get('/check-code/{code}', [JoinController::class, 'checkCode']);

// Protected routes (cần xác thực)
Route::middleware('auth:sanctum')->group(function () {
    
    // ==================== USER MANAGEMENT ====================
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        
        // Lấy danh sách học sinh
        Route::get('/students', [UserController::class, 'getStudents']);
        
        // Lấy danh sách giáo viên
        Route::get('/teachers', [UserController::class, 'getTeachers']);
    });

    // ==================== AUTH ====================
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    // ==================== HOME / DASHBOARD ====================
    Route::get('/home', [HomeController::class, 'index']);
    
    // ==================== JOIN CLASS (Học sinh) ====================
    Route::post('/join-class', [JoinController::class, 'joinByCode']);
    Route::get('/my-classes', [JoinController::class, 'myClasses']);
    Route::delete('/leave-class/{classId}', [JoinController::class, 'leaveClass']);
    
    // ==================== SUBJECTS ====================
    Route::prefix('subjects')->group(function () {
        Route::get('/', [SubjectController::class, 'index']);
        Route::post('/', [SubjectController::class, 'store']);
        Route::get('/{id}', [SubjectController::class, 'show']);
        Route::put('/{id}', [SubjectController::class, 'update']);
        Route::delete('/{id}', [SubjectController::class, 'destroy']);
        
        // Lấy bài thi theo môn
        Route::get('/{id}/exams', [ExamController::class, 'getBySubject']);
    });
    
    // ==================== EXAMS ====================
    Route::prefix('exams')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::post('/', [ExamController::class, 'store']);
        Route::get('/{id}', [ExamController::class, 'show']);
        Route::put('/{id}', [ExamController::class, 'update']);
        Route::delete('/{id}', [ExamController::class, 'destroy']);
        Route::patch('/{id}/status', [ExamController::class, 'updateStatus']);
        Route::post('/{id}/clone', [ExamController::class, 'clone']);
    });
    
    // ==================== QUESTIONS ====================
    Route::prefix('questions')->group(function () {
        Route::get('/', [QuestionController::class, 'index']);
        Route::post('/', [QuestionController::class, 'store']);
        Route::get('/{id}', [QuestionController::class, 'show']);
        Route::put('/{id}', [QuestionController::class, 'update']);
        Route::delete('/{id}', [QuestionController::class, 'destroy']);
        
        // Lấy câu hỏi theo bài thi
        Route::get('/exam/{examId}', [QuestionController::class, 'getByExam']);
            Route::get('/classrooms/{id}/exams', [ExamController::class, 'getByClass']);

    });
    
    // ==================== ANSWERS ====================
    Route::prefix('answers')->group(function () {
        Route::get('/', [AnswerController::class, 'index']);
        Route::post('/', [AnswerController::class, 'store']);
        Route::get('/{id}', [AnswerController::class, 'show']);
        Route::put('/{id}', [AnswerController::class, 'update']);
        Route::delete('/{id}', [AnswerController::class, 'destroy']);
        
        // Lấy đáp án theo câu hỏi
        Route::get('/question/{questionId}', [AnswerController::class, 'getByQuestion']);

    });
    
    // Import
    Route::post('/exams/{examId}/import-questions', [ImportController::class, 'importQuestions']);
    
    // ==================== ATTEMPTS ====================
    Route::prefix('attempts')->group(function () {
        Route::get('/', [AttemptController::class, 'index']);
        Route::post('/', [AttemptController::class, 'store']);
        Route::get('/{id}', [AttemptController::class, 'show']);
        Route::post('/{id}/submit', [AttemptController::class, 'submit']);
        Route::delete('/{id}', [AttemptController::class, 'destroy']);
        
        // Kết quả chi tiết
        Route::get('/{id}/result', [AttemptController::class, 'result']);
        
        // Lấy bài làm theo bài thi
        Route::get('/exam/{examId}', [AttemptController::class, 'getByExam']);
        
        // Lấy bài làm theo học sinh
        Route::get('/user/{userId}', [AttemptController::class, 'getByUser']);
    });
    
    // ==================== ATTEMPT ANSWERS ====================
    Route::prefix('attempts/{attemptId}/questions/{questionId}')->group(function () {
        Route::post('/answer', [AttemptAnswerController::class, 'store']);
        Route::get('/answer', [AttemptAnswerController::class, 'show']);
    });
    
    // ==================== CLASSROOMS ====================
    Route::prefix('classrooms')->group(function () {
        Route::get('/', [ClassroomController::class, 'index']);
        Route::post('/', [ClassroomController::class, 'store']);
        Route::get('/{id}', [ClassroomController::class, 'show']);
        Route::put('/{id}', [ClassroomController::class, 'update']);
        Route::delete('/{id}', [ClassroomController::class, 'destroy']);
        
        // Lấy bài thi trong lớp
        Route::get('/{id}/exams', [ClassroomController::class, 'getClassExams']);
        
        // Mã mời lớp
        Route::get('/{id}/invite-code', [ClassroomController::class, 'getInviteCode']);
        Route::post('/{id}/regenerate-code', [ClassroomController::class, 'regenerateInviteCode']);
        
        // Giao bài thi cho lớp
        Route::post('/{id}/assign-exam', [ClassroomController::class, 'assignExam']);
        
        // Xóa bài thi khỏi lớp
        Route::delete('/{id}/exams/{examId}', [ClassroomController::class, 'removeExam']);
        
        // Lấy học sinh trong lớp
        Route::get('/{id}/students', [ClassStudentController::class, 'getByClassroom']);
    });
    
    // ==================== CLASS STUDENTS ====================
    Route::prefix('class-students')->group(function () {
        Route::get('/', [ClassStudentController::class, 'index']);
        Route::post('/', [ClassStudentController::class, 'store']);
        Route::get('/{id}', [ClassStudentController::class, 'show']);
        Route::put('/{id}', [ClassStudentController::class, 'update']);
        Route::delete('/{id}', [ClassStudentController::class, 'destroy']);
        
        // Thêm/xóa hàng loạt học sinh
        Route::post('/bulk', [ClassStudentController::class, 'bulkStore']);
        Route::delete('/bulk', [ClassStudentController::class, 'bulkDestroy']);
    });
        Route::get('/teachers', [UserController::class, 'getTeachers']);

    
    // ==================== STUDENTS (Legacy) ====================
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/email/{email}', [StudentController::class, 'findByEmail']);
});