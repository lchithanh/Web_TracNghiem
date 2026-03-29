// src/pages/admin/DashboardAdmin.jsx
import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axiosClient from '../../api/axiosClient';

const DashboardAdmin = () => {
  const navigate = useNavigate();
  const [stats, setStats] = useState({
    total_users: 0,
    total_students: 0,
    total_teachers: 0,
    total_exams: 0,
    total_attempts: 0,
    total_subjects: 0,
    total_classes: 0
  });
  const [recentUsers, setRecentUsers] = useState([]);
  const [recentExams, setRecentExams] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      const res = await axiosClient.get('/home');
      const data = res.data?.data || res.data || {};
      
      setStats({
        total_users: data.stats?.total_users || 0,
        total_students: data.stats?.total_students || 0,
        total_teachers: data.stats?.total_teachers || 0,
        total_exams: data.stats?.total_exams || 0,
        total_attempts: data.stats?.total_attempts || 0,
        total_subjects: data.stats?.total_subjects || 0,
        total_classes: data.stats?.total_classes || 0
      });
      
      setRecentUsers(data.recent_users || []);
      setRecentExams(data.recent_exams || []);
      
    } catch (error) {
      console.error('Error:', error);
    } finally {
      setLoading(false);
    }
  };

  const menuItems = [
    { path: '/admin/users', icon: '👥', label: 'Quản lý người dùng', color: 'blue', desc: 'Thêm, sửa, xóa người dùng' },
    { path: '/admin/subjects', icon: '📚', label: 'Quản lý môn học', color: 'green', desc: 'Quản lý tất cả môn học' },
    { path: '/admin/exams', icon: '📝', label: 'Quản lý bài thi', color: 'purple', desc: 'Xem và quản lý bài thi' },
    { path: '/admin/classes', icon: '👥', label: 'Quản lý lớp học', color: 'orange', desc: 'Xem và quản lý lớp học' },
  ];

  const getRoleBadge = (role) => {
    const config = {
      admin: 'bg-red-100 text-red-700',
      teacher: 'bg-blue-100 text-blue-700',
      student: 'bg-green-100 text-green-700'
    };
    const texts = { admin: 'Quản trị', teacher: 'Giảng viên', student: 'Học sinh' };
    return <span className={`px-2 py-1 text-xs rounded-full ${config[role]}`}>{texts[role]}</span>;
  };

  const getExamStatusBadge = (status) => {
    const config = {
      published: 'bg-green-100 text-green-700',
      draft: 'bg-gray-100 text-gray-700',
      closed: 'bg-red-100 text-red-700'
    };
    const texts = { published: 'Đã xuất bản', draft: 'Bản nháp', closed: 'Đã đóng' };
    return <span className={`px-2 py-1 text-xs rounded-full ${config[status]}`}>{texts[status]}</span>;
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        <span className="ml-2 text-gray-500">Đang tải...</span>
      </div>
    );
  }

  return (
    <div className="px-4 sm:px-6 lg:px-8">
      <h1 className="text-xl sm:text-2xl font-bold text-gray-800 mb-6">📊 Tổng quan hệ thống</h1>
      
      {/* Thống kê - Responsive Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {/* Card Tổng người dùng */}
        <div className="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 sm:p-5 rounded-xl shadow-lg hover:shadow-xl transition">
          <div className="flex items-start justify-between">
            <div>
              <p className="text-xs sm:text-sm opacity-90 font-medium">Tổng người dùng</p>
              <p className="text-2xl sm:text-3xl font-bold mt-1">{stats.total_users}</p>
            </div>
            <div className="text-2xl sm:text-3xl opacity-80">👥</div>
          </div>
          <div className="mt-2 sm:mt-3 text-xs opacity-80">
            <span className="inline-flex items-center gap-1">👨‍🎓 {stats.total_students} học sinh</span>
            <span className="inline-flex items-center gap-1 ml-2 sm:ml-3">👨‍🏫 {stats.total_teachers} giảng viên</span>
          </div>
        </div>

        {/* Card Môn học */}
        <div className="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 sm:p-5 rounded-xl shadow-lg hover:shadow-xl transition">
          <div className="flex items-start justify-between">
            <div>
              <p className="text-xs sm:text-sm opacity-90 font-medium">Môn học</p>
              <p className="text-2xl sm:text-3xl font-bold mt-1">{stats.total_subjects}</p>
            </div>
            <div className="text-2xl sm:text-3xl opacity-80">📚</div>
          </div>
          <div className="mt-2 sm:mt-3 text-xs opacity-80">
            Tổng số môn học trong hệ thống
          </div>
        </div>

        {/* Card Bài thi */}
        <div className="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 sm:p-5 rounded-xl shadow-lg hover:shadow-xl transition">
          <div className="flex items-start justify-between">
            <div>
              <p className="text-xs sm:text-sm opacity-90 font-medium">Bài thi</p>
              <p className="text-2xl sm:text-3xl font-bold mt-1">{stats.total_exams}</p>
            </div>
            <div className="text-2xl sm:text-3xl opacity-80">📝</div>
          </div>
          <div className="mt-2 sm:mt-3 text-xs opacity-80">
            {stats.total_attempts} lượt làm bài
          </div>
        </div>

        {/* Card Lớp học */}
        <div className="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-4 sm:p-5 rounded-xl shadow-lg hover:shadow-xl transition">
          <div className="flex items-start justify-between">
            <div>
              <p className="text-xs sm:text-sm opacity-90 font-medium">Lớp học</p>
              <p className="text-2xl sm:text-3xl font-bold mt-1">{stats.total_classes}</p>
            </div>
            <div className="text-2xl sm:text-3xl opacity-80">🏫</div>
          </div>
          <div className="mt-2 sm:mt-3 text-xs opacity-80">
            Lớp học đang hoạt động
          </div>
        </div>
      </div>
      
      {/* Hành động nhanh - Responsive Grid */}
      <div className="mb-8">
        <h2 className="text-base sm:text-lg font-semibold text-gray-800 mb-3">⚡ Hành động nhanh</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
          {menuItems.map(item => (
            <button
              key={item.path}
              onClick={() => navigate(item.path)}
              className={`bg-${item.color}-50 p-4 rounded-xl hover:shadow-md transition-all hover:scale-[1.02] text-left border border-${item.color}-100`}
            >
              <div className="flex items-center gap-3 sm:gap-4">
                <div className="text-3xl sm:text-4xl">{item.icon}</div>
                <div className="flex-1 min-w-0">
                  <div className="font-semibold text-gray-800 text-sm sm:text-base truncate">{item.label}</div>
                  <div className="text-xs text-gray-500 mt-0.5 sm:mt-1 hidden sm:block">{item.desc}</div>
                </div>
              </div>
            </button>
          ))}
        </div>
      </div>
      
      {/* Người dùng và bài thi gần đây */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Người dùng mới */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div className="px-4 sm:px-6 py-3 sm:py-4 border-b bg-gradient-to-r from-gray-50 to-white">
            <div className="flex items-center justify-between">
              <h2 className="text-base sm:text-lg font-semibold text-gray-800">👥 Người dùng mới</h2>
              <span className="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                {recentUsers.length} người
              </span>
            </div>
          </div>
          <div className="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
            {recentUsers.length === 0 ? (
              <div className="p-6 sm:p-8 text-center text-gray-400">
                <div className="text-4xl mb-2">👤</div>
                <p className="text-sm">Chưa có người dùng mới</p>
              </div>
            ) : (
              recentUsers.slice(0, 5).map(user => (
                <div key={user.id} className="px-4 sm:px-6 py-3 hover:bg-gray-50 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                  <div className="flex-1 min-w-0">
                    <p className="font-medium text-gray-800 text-sm sm:text-base truncate">{user.name}</p>
                    <p className="text-xs sm:text-sm text-gray-500 truncate">{user.email}</p>
                  </div>
                  <div className="flex justify-between items-center sm:justify-end gap-3">
                    {getRoleBadge(user.role)}
                    <span className="text-xs text-gray-400 hidden sm:inline">
                      #{user.id}
                    </span>
                  </div>
                </div>
              ))
            )}
          </div>
          <div className="px-4 sm:px-6 py-3 bg-gray-50 border-t">
            <button
              onClick={() => navigate('/admin/users')}
              className="text-xs sm:text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1"
            >
              Xem tất cả
              <svg className="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>
        
        {/* Bài thi gần đây */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div className="px-4 sm:px-6 py-3 sm:py-4 border-b bg-gradient-to-r from-gray-50 to-white">
            <div className="flex items-center justify-between">
              <h2 className="text-base sm:text-lg font-semibold text-gray-800">📝 Bài thi gần đây</h2>
              <span className="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                {recentExams.length} bài
              </span>
            </div>
          </div>
          <div className="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
            {recentExams.length === 0 ? (
              <div className="p-6 sm:p-8 text-center text-gray-400">
                <div className="text-4xl mb-2">📋</div>
                <p className="text-sm">Chưa có bài thi nào</p>
              </div>
            ) : (
              recentExams.slice(0, 5).map(exam => (
                <div key={exam.id} className="px-4 sm:px-6 py-3 hover:bg-gray-50 transition">
                  <div className="flex flex-col gap-2">
                    <div className="flex items-start justify-between">
                      <div className="flex-1 min-w-0">
                        <p className="font-medium text-gray-800 text-sm sm:text-base truncate">{exam.title}</p>
                        <p className="text-xs text-gray-500 mt-0.5">
                          {exam.subject_name || 'Chưa phân môn'}
                        </p>
                      </div>
                      {getExamStatusBadge(exam.status)}
                    </div>
                    <div className="flex items-center justify-between text-xs text-gray-400 mt-1">
                      <div className="flex items-center gap-2">
                        <span>⏰ {exam.duration || 0} phút</span>
                        <span>•</span>
                        <span>📊 {exam.total_questions || 0} câu</span>
                      </div>
                      <span>🕒 {exam.created_at ? new Date(exam.created_at).toLocaleDateString('vi-VN') : 'Mới'}</span>
                    </div>
                  </div>
                </div>
              ))
            )}
          </div>
          <div className="px-4 sm:px-6 py-3 bg-gray-50 border-t">
            <button
              onClick={() => navigate('/admin/exams')}
              className="text-xs sm:text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1"
            >
              Xem tất cả
              <svg className="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DashboardAdmin;