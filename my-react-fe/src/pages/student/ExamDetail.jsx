// src/pages/student/ExamDetail.jsx
import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../../api/axiosClient';

const ExamDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  
  const [exam, setExam] = useState(null);
  const [answers, setAnswers] = useState({});
  const [attemptId, setAttemptId] = useState(null);
  const [timeLeft, setTimeLeft] = useState(null);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [attemptCount, setAttemptCount] = useState(0);
  const [maxAttempts, setMaxAttempts] = useState(1);

  useEffect(() => {
    if (!id) {
      navigate('/student/exams');
      return;
    }
    fetchExamAndCreateAttempt();
  }, [id]);

  const fetchExamAndCreateAttempt = async () => {
    try {
      setLoading(true);
      
      // Lấy chi tiết bài thi
      const examRes = await axiosClient.get(`/exams/${id}`);
      const examData = examRes.data?.data || examRes.data;
      setExam(examData);
      setMaxAttempts(examData.max_attempts || 1);
      setAttemptCount(examData.attempt_count || 0);
      
      // Set thời gian làm bài
      if (examData.duration) {
        setTimeLeft(examData.duration * 60);
      }
      
      // Tạo attempt mới
      const attemptRes = await axiosClient.post('/attempts', { exam_id: parseInt(id) });
      const attemptData = attemptRes.data?.data || attemptRes.data;
      setAttemptId(attemptData.id);
      
      // Khởi tạo answers
      const initialAnswers = {};
      examData.questions?.forEach(q => {
        initialAnswers[q.id] = null;
      });
      setAnswers(initialAnswers);
      
    } catch (error) {
      console.error('Error:', error);
      const message = error.response?.data?.message || 'Không thể tải bài thi';
      alert(message);
      navigate('/student/exams');
    } finally {
      setLoading(false);
    }
  };

  // Timer đếm ngược
  useEffect(() => {
    if (!timeLeft || timeLeft <= 0) return;
    
    const timer = setInterval(() => {
      setTimeLeft(prev => {
        if (prev <= 1) {
          clearInterval(timer);
          return 0;
        }
        return prev - 1;
      });
    }, 1000);
    
    return () => clearInterval(timer);
  }, [timeLeft]);

  const formatTime = (sec) => {
    if (!sec && sec !== 0) return '00:00';
    const minutes = Math.floor(sec / 60);
    const seconds = sec % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  };

  const handleAnswerChange = async (questionId, answerId) => {
    if (!attemptId) return;
    
    setAnswers(prev => ({ ...prev, [questionId]: answerId }));
    
    try {
      await axiosClient.post(`/attempts/${attemptId}/questions/${questionId}/answer`, { 
        answer_id: answerId 
      });
    } catch (error) {
      console.error('Lỗi lưu câu trả lời:', error);
    }
  };

  const handleSubmit = async () => {
    if (!exam || !attemptId) return;
    
    const unanswered = exam.questions?.filter(q => !answers[q.id]).length || 0;
    if (unanswered > 0) {
      if (!window.confirm(`Bạn còn ${unanswered} câu chưa trả lời. Nộp bài?`)) {
        return;
      }
    }
    
    setSubmitting(true);
    try {
      const res = await axiosClient.post(`/attempts/${attemptId}/submit`, {
        time_spent: exam.duration ? exam.duration * 60 - (timeLeft || 0) : null
      });
      const attempt = res.data?.data || res.data;
      navigate(`/student/result/${attempt.id}`);
    } catch (error) {
      alert(error.response?.data?.message || 'Lỗi nộp bài');
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        <span className="ml-2 text-gray-500">Đang tải bài thi...</span>
      </div>
    );
  }
  
  if (!exam) {
    return (
      <div className="text-center py-20">
        <p className="text-red-500">Không tìm thấy bài thi</p>
        <button
          onClick={() => navigate('/student/exams')}
          className="mt-4 bg-blue-600 text-white px-4 py-2 rounded"
        >
          Quay lại
        </button>
      </div>
    );
  }

  const totalQuestions = exam.questions?.length || 0;
  const answeredCount = Object.values(answers).filter(a => a !== null).length;
  const remainingAttempts = maxAttempts - attemptCount;

  return (
    <div className="max-w-3xl mx-auto">
      {/* Header */}
      <div className="bg-white rounded-lg shadow p-4 mb-4 sticky top-0 z-10">
        <div className="flex justify-between items-center">
          <div>
            <h2 className="text-xl font-bold">{exam.title}</h2>
            <p className="text-sm text-gray-500 mt-1">
              Đã trả lời: {answeredCount}/{totalQuestions} câu
            </p>
            <p className="text-xs text-gray-400 mt-1">
              🔄 Lượt đã làm: {attemptCount}/{maxAttempts}
              {remainingAttempts > 0 && ` (còn ${remainingAttempts} lượt)`}
            </p>
          </div>
          {timeLeft !== null && (
            <div className={`text-xl font-mono font-bold ${timeLeft < 60 ? 'text-red-500' : 'text-blue-600'}`}>
              ⏱ {formatTime(timeLeft)}
            </div>
          )}
        </div>
      </div>
      
      {/* Danh sách câu hỏi */}
      <div className="space-y-4">
        {exam.questions?.map((q, index) => (
          <div key={q.id} className="bg-white rounded-lg shadow p-4">
            <p className="font-medium mb-3">
              <span className="text-blue-600">Câu {index + 1}:</span> {q.content}
            </p>
            <div className="space-y-2 ml-4">
              {q.answers?.map((answer) => (
                <label 
                  key={answer.id} 
                  className={`flex items-center p-2 border rounded cursor-pointer transition ${
                    answers[q.id] === answer.id 
                      ? 'border-blue-500 bg-blue-50' 
                      : 'border-gray-200 hover:bg-gray-50'
                  }`}
                >
                  <input
                    type="radio"
                    name={`q-${q.id}`}
                    value={answer.id}
                    checked={answers[q.id] === answer.id}
                    onChange={() => handleAnswerChange(q.id, answer.id)}
                    className="mr-3 w-4 h-4"
                  />
                  <span className="text-gray-700">{answer.content}</span>
                </label>
              ))}
            </div>
          </div>
        ))}
      </div>
      
      {/* Nút nộp bài */}
      <div className="mt-6 flex gap-3">
        <button
          onClick={handleSubmit}
          disabled={submitting}
          className="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50 font-medium"
        >
          {submitting ? 'Đang nộp...' : 'Nộp bài'}
        </button>
        <button
          onClick={() => {
            if (window.confirm('Hủy làm bài? Dữ liệu sẽ không được lưu.')) {
              navigate('/student/exams');
            }
          }}
          className="px-6 bg-gray-500 text-white py-3 rounded-lg hover:bg-gray-600"
        >
          Hủy
        </button>
      </div>
    </div>
  );
};

export default ExamDetail;