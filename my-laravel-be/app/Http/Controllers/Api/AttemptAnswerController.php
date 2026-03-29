<?php
// app/Http/Controllers/Api/AttemptAnswerController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttemptAnswerController extends Controller
{
    /**
     * Lưu câu trả lời
     */
    public function store(Request $request, $attemptId, $questionId)
    {
        $request->validate(['answer_id' => 'required|exists:answers,id']);
        
        $attempt = Attempt::where('user_id', Auth::id())->where('status', 'doing')->findOrFail($attemptId);
        
        $question = $attempt->exam->questions()->find($questionId);
        if (!$question) {
            return response()->json(['success' => false, 'message' => 'Câu hỏi không thuộc bài thi'], 400);
        }
        
        $answer = AttemptAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $questionId],
            ['answer_id' => $request->answer_id]
        );
        
        return response()->json(['success' => true, 'data' => $answer], 201);
    }
    
    /**
     * Lấy câu trả lời
     */
    public function show($attemptId, $questionId)
    {
        $attempt = Attempt::where('user_id', Auth::id())->findOrFail($attemptId);
        
        $answer = AttemptAnswer::where('attempt_id', $attempt->id)
            ->where('question_id', $questionId)
            ->with('answer')
            ->first();
        
        return response()->json(['success' => true, 'data' => $answer]);
    }
}