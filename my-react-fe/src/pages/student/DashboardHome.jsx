// src/pages/student/DashboardHome.jsx
import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axiosClient from '../../api/axiosClient';

const DashboardHome = () => {
  const navigate = useNavigate();
  const [stats, setStats] = useState({
    total_attempts: 0,
    completed: 0,
    avg_score: 0,
    best_score: 0
  });
  const [recentAttempts, setRecentAttempts] = useState([]);
  const [availableExams, setAvailableExams] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const res = await axiosClient.get('/home');
      const data = res.data?.data || res.data || {};
      
      setStats(data.stats || { total_attempts: 0, completed: 0, avg_score: 0, best_score: 0 });
      setRecentAttempts(data.recent_attempts || []);
      setAvailableExams(data.available_exams || []);
      
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <div className="text-center py-10">Đang tải...</div>;

  return (
    <div>
      <h1 className="text-2xl font-bold text-slate-800 mb-6">🏠 Trang chủ</h1>

      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div className="bg-white p-4 rounded-xl shadow hover:shadow-md transition">
          <p className="text-sm text-slate-500">Tổng số bài làm</p>
          <p className="text-2xl font-bold text-slate-800">{stats.total_attempts}</p>
        </div>

        <div className="bg-white p-4 rounded-xl shadow hover:shadow-md transition">
          <p className="text-sm text-slate-500">Đã hoàn thành</p>
          <p className="text-2xl font-bold text-slate-800">{stats.completed}</p>
        </div>

        <div className="bg-white p-4 rounded-xl shadow hover:shadow-md transition">
          <p className="text-sm text-slate-500">Điểm trung bình</p>
          <p className="text-2xl font-bold text-slate-800">{stats.avg_score}</p>
        </div>

        <div className="bg-white p-4 rounded-xl shadow hover:shadow-md transition">
          <p className="text-sm text-slate-500">Điểm cao nhất</p>
          <p className="text-2xl font-bold text-slate-800">{stats.best_score}</p>
        </div>

      </div>
      
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Bài thi có thể làm */}
        <div className="bg-white rounded-xl shadow p-6">
          <h2 className="text-lg font-semibold mb-4 text-slate-800">📝 Bài thi có thể làm</h2>
          {availableExams.length === 0 ? (
            <p className="text-gray-500 text-center py-8">
              {stats.total_attempts === 0 ? 'Chưa có bài thi nào' : 'Bạn đã hoàn thành tất cả bài thi'}
            </p>
          ) : (
            <div className="space-y-3">
              {availableExams.slice(0, 5).map(exam => (
                <div key={exam.id} className="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                  <div>
                    <p className="font-medium">{exam.title}</p>
                    <p className="text-sm text-gray-500">
                      {exam.subject?.name} • {exam.duration} phút
                      {exam.remaining_attempts > 0 && (
                        <span className="ml-2 text-xs text-blue-500">
                          (còn {exam.remaining_attempts} lượt)
                        </span>
                      )}
                    </p>
                  </div>
                  <button
                    onClick={() => navigate(`/student/exams/${exam.id}`)}
                    className="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700"
                  >
                    Làm bài
                  </button>
                </div>
              ))}
            </div>
          )}
        </div>
        
        {/* Bài làm gần đây */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-semibold mb-4">📊 Bài làm gần đây</h2>
          {recentAttempts.length === 0 ? (
            <p className="text-gray-500 text-center py-8">Chưa có bài làm nào</p>
          ) : (
            <div className="space-y-3">
              {recentAttempts.map(attempt => (
                <div key={attempt.id} className="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                  <div>
                    <p className="font-medium">{attempt.exam?.title}</p>
                    <p className="text-sm text-gray-500">
                      Điểm: {attempt.score} • {attempt.exam?.subject?.name}
                      {attempt.attempt_number && attempt.max_attempts && (
                        <span className="ml-2 text-xs text-purple-500">
                          (lượt {attempt.attempt_number}/{attempt.max_attempts})
                        </span>
                      )}
                    </p>
                  </div>
                  <button
                    onClick={() => navigate(`/student/result/${attempt.id}`)}
                    className="px-3 py-1 bg-purple-600 text-white rounded text-sm hover:bg-purple-700"
                  >
                    Xem kết quả
                  </button>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default DashboardHome;