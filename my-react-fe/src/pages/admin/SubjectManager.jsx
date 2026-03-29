// src/pages/admin/SubjectManager.jsx
import React, { useEffect, useState } from 'react';
import axiosClient from '../../api/axiosClient';

const SubjectManager = () => {
  const [subjects, setSubjects] = useState([]);
  const [exams, setExams] = useState([]);
  const [teachers, setTeachers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTeacher, setSearchTeacher] = useState('');
  const [filterSubject, setFilterSubject] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 10;

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const [subjectsRes, examsRes, teachersRes] = await Promise.all([
        axiosClient.get('/subjects'),
        axiosClient.get('/exams'),
        axiosClient.get('/teachers')
      ]);
      setSubjects(subjectsRes.data?.data || subjectsRes.data || []);
      setExams(examsRes.data?.data || examsRes.data || []);
      setTeachers(teachersRes.data?.data || teachersRes.data || []);
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id, name) => {
    const examCount = exams.filter(e => e.subject_id === id).length;
    if (!window.confirm(examCount ? `⚠️ Môn "${name}" có ${examCount} bài thi!\nXóa sẽ mất dữ liệu?` : `Xóa môn "${name}"?`)) return;
    try {
      await axiosClient.delete(`/subjects/${id}`);
      alert('Xóa thành công!');
      fetchData();
    } catch (error) {
      alert(error.response?.data?.message || 'Lỗi xóa');
    }
  };

  const getSubjectsByTeacher = () => {
    if (!searchTeacher) return subjects;
    const teacherExams = exams.filter(e => {
      const t = teachers.find(t => t.id === e.created_by);
      return t?.name?.toLowerCase().includes(searchTeacher.toLowerCase());
    });
    const subjectIds = [...new Set(teacherExams.map(e => e.subject_id))];
    return subjects.filter(s => subjectIds.includes(s.id));
  };

  const availableSubjects = getSubjectsByTeacher();

  const filteredSubjects = subjects.filter(s => {
    if (searchTeacher) {
      const hasTeacher = exams.some(e => {
        if (e.subject_id !== s.id) return false;
        const t = teachers.find(t => t.id === e.created_by);
        return t?.name?.toLowerCase().includes(searchTeacher.toLowerCase());
      });
      if (!hasTeacher) return false;
    }
    if (filterSubject && s.id != filterSubject) return false;
    return true;
  });

  const indexOfLast = currentPage * itemsPerPage;
  const indexOfFirst = indexOfLast - itemsPerPage;
  const currentSubjects = filteredSubjects.slice(indexOfFirst, indexOfLast);
  const totalPages = Math.ceil(filteredSubjects.length / itemsPerPage);

  useEffect(() => {
    setCurrentPage(1);
    if (filterSubject && !availableSubjects.some(s => s.id == filterSubject)) setFilterSubject('');
  }, [searchTeacher]);

  if (loading) return <div className="text-center py-10">Đang tải...</div>;

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">📚 Quản lý môn học</h1>
        <div className="text-sm text-gray-500">Tổng: {subjects.length} môn</div>
      </div>

      {/* Bộ lọc */}
      <div className="bg-white rounded shadow p-4 mb-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-sm font-medium mb-1">Tìm theo giảng viên</label>
            <input
              type="text"
              placeholder="Nhập tên giảng viên..."
              value={searchTeacher}
              onChange={(e) => setSearchTeacher(e.target.value)}
              className="w-full p-2 border rounded"
            />
          </div>
          <div>
            <label className="block text-sm font-medium mb-1">Lọc theo môn học</label>
            <select
              value={filterSubject}
              onChange={(e) => setFilterSubject(e.target.value)}
              className="w-full p-2 border rounded"
              disabled={availableSubjects.length === 0 && searchTeacher}
            >
              <option value="">Tất cả môn</option>
              {availableSubjects.map(s => (
                <option key={s.id} value={s.id}>{s.name}</option>
              ))}
            </select>
          </div>
        </div>
      </div>

      {/* Bảng */}
      {currentSubjects.length === 0 ? (
        <div className="bg-white rounded shadow p-12 text-center text-gray-500">
          {searchTeacher || filterSubject ? 'Không tìm thấy' : 'Chưa có môn học'}
        </div>
      ) : (
        <>
          <div className="bg-white rounded shadow overflow-x-auto">
            <table className="w-full min-w-[700px]">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-4 py-3 text-left text-sm">STT</th>
                  <th className="px-4 py-3 text-left text-sm">Tên môn</th>
                  <th className="px-4 py-3 text-left text-sm">Giảng viên</th>
                  <th className="px-4 py-3 text-left text-sm">Số bài thi</th>
                  <th className="px-4 py-3 text-left text-sm">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                {currentSubjects.map((subject, idx) => {
                  const examCount = exams.filter(e => e.subject_id === subject.id).length;
                  const teacherNames = [...new Set(
                    exams.filter(e => e.subject_id === subject.id)
                      .map(e => teachers.find(t => t.id === e.created_by)?.name)
                      .filter(Boolean)
                  )].join(', ');
                  return (
                    <tr key={subject.id} className="border-t hover:bg-gray-50">
                      <td className="px-4 py-3">{indexOfFirst + idx + 1}</td>
                      <td className="px-4 py-3 font-medium">{subject.name}</td>
                      <td className="px-4 py-3 text-sm text-gray-500">{teacherNames || '—'}</td>
                      <td className="px-4 py-3">
                        <span className={`px-2 py-1 rounded-full text-xs ${examCount ? 'bg-yellow-100' : 'bg-green-100'}`}>
                          {examCount} bài
                        </span>
                      </td>
                      <td className="px-4 py-3">
                        <button
                          onClick={() => handleDelete(subject.id, subject.name)}
                          className="text-red-600 hover:text-red-800"
                        >
                          Xóa
                        </button>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>

          {/* Phân trang */}
          {totalPages > 1 && (
            <div className="flex flex-wrap justify-center gap-2 mt-6">
              <button
                onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
                disabled={currentPage === 1}
                className="px-3 py-1 border rounded text-sm"
              >
                «
              </button>
              {[...Array(totalPages)].map((_, i) => (
                <button
                  key={i + 1}
                  onClick={() => setCurrentPage(i + 1)}
                  className={`px-3 py-1 rounded text-sm ${
                    currentPage === i + 1 ? 'bg-blue-600 text-white' : 'border'
                  }`}
                >
                  {i + 1}
                </button>
              ))}
              <button
                onClick={() => setCurrentPage(p => Math.min(totalPages, p + 1))}
                disabled={currentPage === totalPages}
                className="px-3 py-1 border rounded text-sm"
              >
                »
              </button>
            </div>
          )}
        </>
      )}
    </div>
  );
};

export default SubjectManager;