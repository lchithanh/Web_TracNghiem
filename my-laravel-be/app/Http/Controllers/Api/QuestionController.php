<?php
// app/Http/Controllers/Api/QuestionController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    /**
     * Danh sách câu hỏi theo bài thi
     */
    public function index(Request $request)
    {
        try {
            $examId = $request->exam_id;
            if (!$examId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu exam_id'
                ], 400);
            }
            
            $user = Auth::user();
            $exam = Exam::find($examId);
            
            if (!$exam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài thi'
                ], 404);
            }
            
            if ($user->role === 'teacher' && $exam->created_by != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            $questions = Question::where('exam_id', $examId)
                ->with('answers')
                ->orderBy('id')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $questions,
                'exam' => [
                    'id' => $exam->id,
                    'title' => $exam->title
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Tạo câu hỏi mới
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            $validated = $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'content' => 'required|string',
                'answers' => 'required|array|min:2',
                'answers.*.content' => 'required|string',
                'answers.*.is_correct' => 'required|boolean',
            ]);
            
            $exam = Exam::find($validated['exam_id']);
            if (!$exam || $exam->created_by != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            DB::beginTransaction();
            
            $question = Question::create([
                'exam_id' => $validated['exam_id'],
                'content' => $validated['content'],
            ]);
            
            foreach ($validated['answers'] as $ans) {
                $question->answers()->create([
                    'content' => $ans['content'],
                    'is_correct' => $ans['is_correct'] ? 1 : 0
                ]);
            }
            
            $exam->total_questions = $exam->questions()->count();
            $exam->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Thêm câu hỏi thành công',
                'data' => $question->load('answers')
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
     * Cập nhật câu hỏi
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            $question = Question::findOrFail($id);
            $exam = Exam::find($question->exam_id);
            
            if (!$exam || $exam->created_by != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            $validated = $request->validate([
                'content' => 'sometimes|string',
                'answers' => 'sometimes|array|min:2',
                'answers.*.id' => 'nullable|exists:answers,id',
                'answers.*.content' => 'required_with:answers|string',
                'answers.*.is_correct' => 'required_with:answers|boolean',
            ]);
            
            DB::beginTransaction();
            
            if (isset($validated['content'])) {
                $question->update(['content' => $validated['content']]);
            }
            
            if (isset($validated['answers'])) {
                $existingIds = $question->answers()->pluck('id')->toArray();
                $updatedIds = [];
                
                foreach ($validated['answers'] as $ans) {
                    if (isset($ans['id'])) {
                        $answer = $question->answers()->find($ans['id']);
                        if ($answer) {
                            $answer->update([
                                'content' => $ans['content'],
                                'is_correct' => $ans['is_correct'] ? 1 : 0
                            ]);
                            $updatedIds[] = $answer->id;
                        }
                    } else {
                        $new = $question->answers()->create([
                            'content' => $ans['content'],
                            'is_correct' => $ans['is_correct'] ? 1 : 0
                        ]);
                        $updatedIds[] = $new->id;
                    }
                }
                
                $toDelete = array_diff($existingIds, $updatedIds);
                if (!empty($toDelete)) {
                    $question->answers()->whereIn('id', $toDelete)->delete();
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công',
                'data' => $question->load('answers')
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
 * Xóa câu hỏi - Xóa luôn cả đáp án
 */
public function destroy($id)
{
    try {
        $user = Auth::user();
        $question = Question::findOrFail($id);
        $exam = Exam::find($question->exam_id);
        
        // Kiểm tra quyền
        if ($user->role !== 'admin' && (!$exam || $exam->created_by != $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa câu hỏi này'
            ], 403);
        }
        
        $answerCount = $question->answers()->count();
        
        DB::beginTransaction();
        
        // Xóa đáp án trước (do foreign key)
        $question->answers()->delete();
        
        // Xóa câu hỏi
        $question->delete();
        
        // Cập nhật tổng số câu hỏi của bài thi
        if ($exam) {
            $exam->total_questions = $exam->questions()->count();
            $exam->save();
        }
        
        DB::commit();
        
        $message = "Xóa câu hỏi thành công";
        if ($answerCount > 0) {
            $message .= " và {$answerCount} đáp án";
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'deleted_answers' => $answerCount
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ], 500);
    }
}

}