<?php
// app/Http/Controllers/Api/AnswerController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnswerController extends Controller
{
    /**
     * Danh sách đáp án
     */
    public function index(Request $request)
    {
        try {
            $query = Answer::query();
            
            if ($request->has('question_id')) {
                $query->where('question_id', $request->question_id);
            }
            
            $answers = $query->get();
            
            return response()->json([
                'success' => true,
                'data' => $answers
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách đáp án'
            ], 500);
        }
    }

    /**
     * Chi tiết đáp án
     */
    public function show($id)
    {
        try {
            $answer = Answer::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $answer
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đáp án'
            ], 404);
        }
    }

    /**
     * Tạo đáp án mới
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền tạo đáp án'
                ], 403);
            }
            
            $validated = $request->validate([
                'question_id' => 'required|exists:questions,id',
                'content' => 'required|string',
                'is_correct' => 'required|boolean',
            ]);
            
            $question = Question::find($validated['question_id']);
            $exam = Exam::find($question->exam_id);
            
            if (!$exam || $exam->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thêm đáp án cho câu hỏi này'
                ], 403);
            }
            
            $answer = Answer::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Thêm đáp án thành công',
                'data' => $answer
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo đáp án'
            ], 500);
        }
    }

    /**
     * Cập nhật đáp án
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật đáp án'
                ], 403);
            }
            
            $answer = Answer::findOrFail($id);
            
            $question = Question::find($answer->question_id);
            $exam = Exam::find($question->exam_id);
            
            if (!$exam || $exam->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền sửa đáp án này'
                ], 403);
            }
            
            $validated = $request->validate([
                'content' => 'sometimes|string',
                'is_correct' => 'sometimes|boolean',
            ]);
            
            $answer->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đáp án thành công',
                'data' => $answer
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật đáp án'
            ], 500);
        }
    }

    /**
     * Xóa đáp án
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa đáp án'
                ], 403);
            }
            
            $answer = Answer::findOrFail($id);
            
            $question = Question::find($answer->question_id);
            $exam = Exam::find($question->exam_id);
            
            if (!$exam || $exam->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa đáp án này'
                ], 403);
            }
            
            $answer->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa đáp án thành công'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa đáp án'
            ], 500);
        }
    }
}