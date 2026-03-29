// src/pages/teacher/ClassroomDetail.jsx
import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../../api/axiosClient';

const ClassroomDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  
  const [classroom, setClassroom] = useState(null);
  const [students, setStudents] = useState([]);
  const [assignedExams, setAssignedExams] = useState([]);
  const [availableExams, setAvailableExams] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showAssignModal, setShowAssignModal] = useState(false);
  const [selectedExamId, setSelectedExamId] = useState('');

  useEffect(() => {
    fetchData();
  }, [id]);

  const fetchData = async () => {
    try {
      setLoading(true);
      
      // Lấy thông tin lớp
      const classRes = await axiosClient.get(`/classrooms/${id}`);
      setClassroom(classRes.data?.data);
      
      // Lấy danh sách học sinh (bao gồm student_code)
      const studentsRes = await axiosClient.get(`/class-students?classroom_id=${id}`);
      setStudents(studentsRes.data?.data || []);
      
      // Lấy bài thi đã giao
      const assignedRes = await axiosClient.get(`/classrooms/${id}/exams`);
      setAssignedExams(assignedRes.data?.data || []);
      
      // Lấy tất cả bài thi của giáo viên
      const examsRes = await axiosClient.get('/exams');
      const allExams = examsRes.data?.data || [];
      
      // Lọc bài thi chưa giao
      const assignedIds = assignedExams.map(e => e.id);
      const available = allExams.filter(e => !assignedIds.includes(e.id));
      setAvailableExams(available);
      
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleAssignExam = async () => {
    if (!selectedExamId) {
      alert('Vui lòng chọn bài thi');
      return;
    }
    
    try {
      await axiosClient.post(`/classrooms/${id}/assign-exam`, {
        exam_id: selectedExamId
      });
      alert('Giao bài thi thành công');
      setShowAssignModal(false);
      setSelectedExamId('');
      fetchData();
    } catch (err) {
      alert(err.response?.data?.message || 'Lỗi giao bài');
    }
  };

  const handleRemoveExam = async (examId) => {
    if (!window.confirm('Xóa bài thi khỏi lớp?')) return;
    try {
      await axiosClient.delete(`/classrooms/${id}/exams/${examId}`);
      fetchData();
      alert('Xóa thành công');
    } catch (err) {
      alert(err.response?.data?.message || 'Lỗi xóa');
    }
  };

  const handleRemoveStudent = async (studentId, studentName) => {
    if (!window.confirm(`Xóa học sinh "${studentName}" khỏi lớp?`)) return;
    try {
      await axiosClient.delete(`/class-students/${studentId}`);
      fetchData();
    } catch (err) {
      alert(err.response?.data?.message || 'Lỗi xóa');
    }
  };

  if (loading) return <div className="text-center py-10">Đang tải...</div>;

  return (
    <div>
      <button
        onClick={() => navigate('/teacher/classes')}
        className="text-blue-600 hover:text-blue-800 mb-4 inline-block"
      >
        ← Quay lại
      </button>
      
      <div className="bg-white rounded shadow p-6 mb-6">
        <h1 className="text-2xl font-bold">{classroom?.name}</h1>
        {classroom?.description && (
          <p className="text-gray-500 mt-1">{classroom.description}</p>
        )}
        <div className="flex gap-4 mt-2">
          <p className="text-sm text-gray-400">Số học sinh: {students.length}</p>
          <p className="text-sm text-gray-400">Mã lớp: {classroom?.invite_code}</p>
        </div>
      </div>
      
      {/* Bài thi đã giao */}
      <div className="mb-6">
        <div className="flex justify-between items-center mb-4">
          <h2 className="text-xl font-bold">Bài thi đã giao</h2>
          <button
            onClick={() => setShowAssignModal(true)}
            className="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
          >
            + Giao bài thi
          </button>
        </div>
        
        {assignedExams.length === 0 ? (
          <div className="bg-white rounded shadow p-8 text-center text-gray-500">
            Chưa có bài thi nào được giao
          </div>
        ) : (
          <div className="space-y-2">
            {assignedExams.map(exam => (
              <div key={exam.id} className="bg-white rounded shadow p-4 flex justify-between items-center">
                <div>
                  <h3 className="font-semibold">{exam.title}</h3>
                  <p className="text-sm text-gray-500">{exam.subject?.name}</p>
                  <p className="text-xs text-gray-400">Thời gian: {exam.duration} phút</p>
                </div>
                <button
                  onClick={() => handleRemoveExam(exam.id)}
                  className="text-red-600 hover:text-red-800"
                >
                  Xóa
                </button>
              </div>
            ))}
          </div>
        )}
      </div>
      
      {/* Danh sách học sinh */}
      <div>
        <h2 className="text-xl font-bold mb-4">Học sinh trong lớp</h2>
        {students.length === 0 ? (
          <div className="bg-white rounded shadow p-8 text-center text-gray-500">
            Chưa có học sinh
          </div>
        ) : (
          <div className="bg-white rounded shadow overflow-hidden">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-4 py-3 text-left text-sm font-semibold">STT</th>
                  <th className="px-4 py-3 text-left text-sm font-semibold">Họ tên</th>
                  <th className="px-4 py-3 text-left text-sm font-semibold">Mã sinh viên</th>
                  <th className="px-4 py-3 text-left text-sm font-semibold">Email</th>
                  <th className="px-4 py-3 text-left text-sm font-semibold">Ngày tham gia</th>
                  <th className="px-4 py-3 text-left text-sm font-semibold">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                {students.map((s, index) => (
                  <tr key={s.id} className="border-t hover:bg-gray-50">
                    <td className="px-4 py-3 text-sm text-gray-500">{index + 1}</td>
                    <td className="px-4 py-3">
                      <div className="flex items-center">
                        <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-sm">
                          {s.student?.name?.charAt(0).toUpperCase() || '?'}
                        </div>
                        <span className="ml-2 font-medium">{s.student?.name}</span>
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <span className="text-sm font-mono text-gray-700">
                        {s.student?.student_code || '—'}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-sm text-gray-500">{s.student?.email}</td>
                    <td className="px-4 py-3 text-sm text-gray-500">
                      {new Date(s.created_at).toLocaleDateString('vi-VN')}
                    </td>
                    <td className="px-4 py-3">
                      <button
                        onClick={() => handleRemoveStudent(s.id, s.student?.name)}
                        className="text-red-600 hover:text-red-800 text-sm"
                      >
                        Xóa
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
      
      {/* Modal giao bài thi */}
      {showAssignModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded p-6 w-full max-w-md">
            <h2 className="text-xl font-bold mb-4">Giao bài thi cho lớp</h2>
            <select
              value={selectedExamId}
              onChange={(e) => setSelectedExamId(e.target.value)}
              className="w-full p-2 border rounded mb-4"
            >
              <option value="">Chọn bài thi</option>
              {availableExams.map(exam => (
                <option key={exam.id} value={exam.id}>
                  {exam.title} - {exam.subject?.name} ({exam.duration} phút)
                </option>
              ))}
            </select>
            <div className="flex gap-3">
              <button
                onClick={handleAssignExam}
                className="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
              >
                Giao
              </button>
              <button
                onClick={() => setShowAssignModal(false)}
                className="flex-1 bg-gray-300 py-2 rounded hover:bg-gray-400"
              >
                Hủy
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default ClassroomDetail;