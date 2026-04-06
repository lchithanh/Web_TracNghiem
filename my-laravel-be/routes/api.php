<?php
// routes/api.php

use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherManagerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\JoinController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\AttemptController;
use App\Http\Controllers\Api\AttemptAnswerController;
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\ClassStudentController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\StudentResultController;

/*
|--------------------------------------------------------------------------
| Public routes (No auth required)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/check-code/{code}', [JoinController::class, 'checkCode']);

/*
|--------------------------------------------------------------------------
| Protected routes (Require auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // ---------------- USER ----------------
    Route::apiResource('users', UserController::class);
    Route::get('users/students', [UserController::class, 'getStudents']);
    Route::get('users/teachers', [UserController::class, 'getTeachers']);

   
Route::get('/teachers', [TeacherManagerController::class, 'index']);
Route::get('/teachers/{id}/subjects', [TeacherManagerController::class, 'getSubjects']);


    // ---------------- AUTH ----------------
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('profile', [AuthController::class, 'updateProfile']);

    // ---------------- DASHBOARD ----------------
    Route::get('home', [HomeController::class, 'index']);

    // ---------------- JOIN CLASS ----------------
    Route::post('join-class', [JoinController::class, 'joinByCode']);
    Route::get('my-classes', [JoinController::class, 'myClasses']);
    Route::delete('leave-class/{classId}', [JoinController::class, 'leaveClass']);

    // ---------------- SUBJECTS ----------------
    Route::prefix('subjects')->group(function () {
        Route::get('/', [SubjectController::class, 'index']);
        Route::post('/', [SubjectController::class, 'store']);
        Route::get('/{id}', [SubjectController::class, 'show']);
        Route::put('/{id}', [SubjectController::class, 'update']);
        Route::delete('/{id}', [SubjectController::class, 'destroy']);

        // Teacher assignment
        Route::get('{id}/teachers', [SubjectController::class, 'getTeachers']);
        Route::get('{id}/available-teachers', [SubjectController::class, 'getAvailableTeachers']);
        Route::post('{id}/teachers', [SubjectController::class, 'assignTeacher']);
        Route::delete('{id}/teachers/{teacherId}', [SubjectController::class, 'unassignTeacher']);
    });

    // ---------------- EXAMS ----------------
    Route::apiResource('exams', ExamController::class);
    Route::patch('exams/{id}/status', [ExamController::class, 'updateStatus']);
    Route::post('exams/{id}/clone', [ExamController::class, 'clone']);

    // ---------------- QUESTIONS ----------------
    Route::apiResource('questions', QuestionController::class);
    Route::get('questions/exam/{examId}', [QuestionController::class, 'getByExam']);
    Route::get('classrooms/{id}/exams', [ExamController::class, 'getByClass']);

    // ---------------- ANSWERS ----------------
    Route::apiResource('answers', AnswerController::class);
    Route::get('answers/question/{questionId}', [AnswerController::class, 'getByQuestion']);

    // ---------------- IMPORT ----------------
    Route::post('exams/{examId}/import-questions', [ImportController::class, 'importQuestions']);

    // ---------------- ATTEMPTS ----------------
    Route::prefix('attempts')->group(function () {
        Route::apiResource('/', AttemptController::class)->parameters(['' => 'id']);
        Route::post('{id}/submit', [AttemptController::class, 'submit']);
        Route::get('{id}/result', [AttemptController::class, 'result']);
        Route::get('exam/{examId}', [AttemptController::class, 'getByExam']);
        Route::get('user/{userId}', [AttemptController::class, 'getByUser']);

        // Attempt answers
        Route::prefix('{attemptId}/questions/{questionId}')->group(function () {
            Route::post('answer', [AttemptAnswerController::class, 'store']);
            Route::get('answer', [AttemptAnswerController::class, 'show']);
        });
    });

    // ---------------- CLASSROOMS ----------------
    Route::apiResource('classrooms', ClassroomController::class);
    Route::get('classrooms/{id}/exams', [ClassroomController::class, 'getClassExams']);
    Route::get('classrooms/{id}/invite-code', [ClassroomController::class, 'getInviteCode']);
    Route::post('classrooms/{id}/regenerate-code', [ClassroomController::class, 'regenerateInviteCode']);
    Route::post('classrooms/{id}/assign-exam', [ClassroomController::class, 'assignExam']);
    Route::delete('classrooms/{id}/exams/{examId}', [ClassroomController::class, 'removeExam']);
    Route::get('classrooms/{id}/students', [ClassStudentController::class, 'getByClassroom']);
    
    

    // ---------------- CLASS STUDENTS ----------------
    Route::apiResource('class-students', ClassStudentController::class);
    Route::post('class-students/bulk', [ClassStudentController::class, 'bulkStore']);
    Route::delete('class-students/bulk', [ClassStudentController::class, 'bulkDestroy']);

    // ---------------- LEGACY ----------------
    Route::get('students', [StudentController::class, 'index']);
    Route::get('students/email/{email}', [StudentController::class, 'findByEmail']);
});