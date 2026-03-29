<?php
// app/Http/Controllers/Api/ImportController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;

class ImportController extends Controller
{
    public function importQuestions(Request $request, $examId)
    {
        try {
            $user = Auth::user();
            $exam = Exam::findOrFail($examId);
            
            if ($user->role !== 'teacher' || $exam->created_by != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền'
                ], 403);
            }
            
            $request->validate([
                'file' => 'required|file|mimes:doc,docx,txt|max:10240'
            ]);
            
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            if ($extension === 'docx') {
                $questions = $this->parseDocxWithFormat($file);
            } else {
                $questions = $this->parseTxt($file);
            }
            
            if (empty($questions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy câu hỏi trong file'
                ], 400);
            }
            
            DB::beginTransaction();
            
            $imported = 0;
            
            foreach ($questions as $q) {
                $questionId = DB::table('questions')->insertGetId([
                    'exam_id' => $examId,
                    'content' => $q['content']
                ]);
                
                foreach ($q['answers'] as $ans) {
                    DB::table('answers')->insert([
                        'question_id' => $questionId,
                        'content' => $ans['content'],
                        'is_correct' => $ans['is_correct'] ? 1 : 0
                    ]);
                }
                $imported++;
            }
            
            $exam->total_questions = DB::table('questions')->where('exam_id', $examId)->count();
            $exam->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Import thành công {$imported} câu hỏi"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Parse file .docx với định dạng (in đậm, màu sắc)
     */
    private function parseDocxWithFormat($file)
    {
        $phpWord = IOFactory::load($file->getPathname());
        $paragraphs = [];
        
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getElements')) {
                    $lineText = '';
                    $isCorrect = false;
                    
                    foreach ($element->getElements() as $textElement) {
                        if ($textElement instanceof Text) {
                            $text = $textElement->getText();
                            $fontStyle = $textElement->getFontStyle();
                            
                            // Kiểm tra định dạng để xác định đáp án đúng
                            if ($fontStyle) {
                                // 1. In đậm
                                if ($fontStyle->isBold()) {
                                    $isCorrect = true;
                                    $text = "**" . $text . "**";
                                }
                                // 2. Màu chữ (xanh, đỏ, v.v)
                                if ($fontStyle->getColor()) {
                                    $color = strtoupper($fontStyle->getColor());
                                    // Màu xanh, đỏ, xanh lá được coi là đáp án đúng
                                    if (in_array($color, ['00FF00', '008000', 'GREEN', '0000FF', 'BLUE', 'FF0000', 'RED'])) {
                                        $isCorrect = true;
                                        $text = "**" . $text . "**";
                                    }
                                }
                                // 3. Nền màu
                                if ($fontStyle->getBgColor()) {
                                    $isCorrect = true;
                                    $text = "**" . $text . "**";
                                }
                            }
                            
                            $lineText .= $text;
                        }
                    }
                    
                    if (!empty(trim($lineText))) {
                        $paragraphs[] = [
                            'text' => $lineText,
                            'is_correct' => $isCorrect
                        ];
                    }
                }
            }
        }
        
        // Chuyển thành text và parse
        $text = '';
        foreach ($paragraphs as $p) {
            $text .= $p['text'] . "\n";
        }
        
        return $this->parseTextContentWithMarkers($text);
    }
    
    /**
     * Parse file .txt
     */
    private function parseTxt($file)
    {
        $content = file_get_contents($file->getPathname());
        $content = $this->fixEncoding($content);
        return $this->parseTextContentWithMarkers($content);
    }
    
    /**
     * Fix encoding
     */
    private function fixEncoding($text)
    {
        $text = preg_replace('/^\xEF\xBB\xBF/', '', $text);
        $encodings = ['UTF-8', 'Windows-1252', 'Windows-1258', 'ISO-8859-1'];
        
        foreach ($encodings as $encoding) {
            if (mb_check_encoding($text, $encoding)) {
                $converted = mb_convert_encoding($text, 'UTF-8', $encoding);
                if (mb_check_encoding($converted, 'UTF-8')) {
                    return $converted;
                }
            }
        }
        return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }
    
    /**
     * Parse nội dung text với các marker (**)
     */
    private function parseTextContentWithMarkers($content)
    {
        $questions = [];
        $lines = explode("\n", $content);
        
        $currentQuestion = null;
        $currentAnswers = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                if ($currentQuestion && count($currentAnswers) >= 2) {
                    $questions[] = [
                        'content' => $currentQuestion,
                        'answers' => $currentAnswers
                    ];
                    $currentQuestion = null;
                    $currentAnswers = [];
                }
                continue;
            }
            
            // Nhận diện câu hỏi: bắt đầu bằng số
            if (preg_match('/^(\d+)[\.\)]\s*(.+)/', $line, $matches) ||
                preg_match('/^Câu\s+(\d+)[:\.\)]\s*(.+)/i', $line, $matches)) {
                
                if ($currentQuestion && count($currentAnswers) >= 2) {
                    $questions[] = [
                        'content' => $currentQuestion,
                        'answers' => $currentAnswers
                    ];
                }
                $currentQuestion = trim($matches[2]);
                $currentAnswers = [];
                continue;
            }
            
            // Nhận diện đáp án: bắt đầu bằng A., B., C., D.
            if (preg_match('/^([A-D])[\.\)]\s*(.+)/i', $line, $matches)) {
                $answerContent = trim($matches[2]);
                $isCorrect = false;
                
                // Kiểm tra các dấu hiệu đáp án đúng
                // 1. Có ** (in đậm từ Word)
                if (strpos($answerContent, '**') !== false) {
                    $isCorrect = true;
                    $answerContent = str_replace('**', '', $answerContent);
                }
                // 2. Có dấu *
                elseif (strpos($answerContent, '*') !== false) {
                    $isCorrect = true;
                    $answerContent = str_replace('*', '', $answerContent);
                }
                // 3. Có dấu ✓
                elseif (strpos($answerContent, '✓') !== false) {
                    $isCorrect = true;
                    $answerContent = str_replace('✓', '', $answerContent);
                }
                // 4. Có (Đúng) hoặc (đúng)
                elseif (stripos($answerContent, '(đúng)') !== false) {
                    $isCorrect = true;
                    $answerContent = preg_replace('/\(đúng\)/i', '', $answerContent);
                }
                // 5. Có (màu xanh) - dấu hiệu từ file Word
                elseif (stripos($answerContent, '(màu xanh)') !== false) {
                    $isCorrect = true;
                    $answerContent = preg_replace('/\(màu xanh\)/i', '', $answerContent);
                }
                // 6. Có (in đậm)
                elseif (stripos($answerContent, '(in đậm)') !== false) {
                    $isCorrect = true;
                    $answerContent = preg_replace('/\(in đậm\)/i', '', $answerContent);
                }
                
                $answerContent = trim($answerContent);
                $currentAnswers[] = [
                    'content' => $answerContent,
                    'is_correct' => $isCorrect
                ];
                continue;
            }
            
            // Nội dung câu hỏi dài
            if ($currentQuestion && empty($currentAnswers)) {
                $currentQuestion .= ' ' . $line;
            }
        }
        
        // Lưu câu hỏi cuối
        if ($currentQuestion && count($currentAnswers) >= 2) {
            $questions[] = [
                'content' => $currentQuestion,
                'answers' => $currentAnswers
            ];
        }
        
        // Validate và đảm bảo có ít nhất 1 đáp án đúng
        $valid = [];
        foreach ($questions as $q) {
            if (empty(trim($q['content']))) continue;
            if (count($q['answers']) < 2) continue;
            
            $hasCorrect = false;
            foreach ($q['answers'] as $a) {
                if ($a['is_correct']) {
                    $hasCorrect = true;
                    break;
                }
            }
            if (!$hasCorrect && count($q['answers']) > 0) {
                $q['answers'][0]['is_correct'] = true;
            }
            
            $valid[] = $q;
        }
        
        return $valid;
    }
}