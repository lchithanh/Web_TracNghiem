// src/pages/teacher/ExamManager.jsx
import React, { useEffect, useState } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import axiosClient from '../../api/axiosClient';

const ExamManager = () => {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const filterSubjectId = searchParams.get('subject_id');

  const [exams, setExams] = useState([]);
  const [subjects, setSubjects] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editingExam, setEditingExam] = useState(null);
  const [selectedSubjectId, setSelectedSubjectId] = useState(filterSubjectId || '');
  const [formData, setFormData] = useState({
    title: '', subject_id: '', duration: 30, max_attempts: 1, status: 'draft'
  });

  useEffect(() => { fetchData(); }, [selectedSubjectId]);

  const fetchData = async () => {
    try {
      setLoading(true);
      const subRes = await axiosClient.get('/subjects');
      setSubjects(subRes.data?.data || subRes.data || []);
      const url = selectedSubjectId ? `/exams?subject_id=${selectedSubjectId}` : '/exams';
      const exRes = await axiosClient.get(url);
      setExams(exRes.data?.data || exRes.data || []);
    } catch (e) { console.error(e); }
    finally { setLoading(false); }
  };

  const openModal = (exam = null) => {
    setEditingExam(exam);
    setFormData(exam ? {
      title: exam.title || '', subject_id: exam.subject_id || '',
      duration: exam.duration || 30, max_attempts: exam.max_attempts || 1, status: exam.status || 'draft'
    } : { title: '', subject_id: selectedSubjectId || '', duration: 30, max_attempts: 1, status: 'draft' });
    setShowModal(true);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const data = { ...formData };
    try {
      if (editingExam) {
        await axiosClient.put(`/exams/${editingExam.id}`, data);
        alert('Cập nhật thành công');
        setExams(exams.map(ex => ex.id === editingExam.id ? { ...ex, ...data } : ex));
      } else {
        const res = await axiosClient.post('/exams', data);
        alert('Tạo bài thi thành công');
        setExams([res.data?.data, ...exams]);
      }
      setShowModal(false);
    } catch (e) { alert(e.response?.data?.message || 'Lỗi'); }
  };

  const handleDelete = async (id, title) => {
    try {
      await axiosClient.delete(`/exams/${id}`);
      alert('Xóa thành công');
      setExams(exams.filter(ex => ex.id !== id));
    } catch (error) {
      const d = error.response?.data;
      if (d?.requires_confirmation) {
        let msg = `⚠️ ${d.message}\n`;
        d.warnings?.forEach(w => { msg += `• ${w}\n`; });
        msg += '\nXóa tất cả dữ liệu liên quan?';
        if (window.confirm(msg)) {
          try {
            await axiosClient.delete(`/exams/${id}`, { params: { force: true } });
            alert('Đã xóa');
            setExams(exams.filter(ex => ex.id !== id));
          } catch (fe) { alert(fe.response?.data?.message || 'Lỗi'); }
        }
      } else { alert(d?.message || 'Lỗi xóa'); }
    }
  };

  const handleStatusChange = async (id, status) => {
    try {
      await axiosClient.patch(`/exams/${id}/status`, { status });
      setExams(exams.map(ex => ex.id === id ? { ...ex, status } : ex));
    } catch (e) { alert(e.response?.data?.message || 'Lỗi'); }
  };

  const statusStyle = {
    published: 'bg-emerald-100 text-emerald-700',
    draft:     'bg-amber-100 text-amber-700',
    closed:    'bg-gray-100 text-gray-500',
  };
  const statusLabel = { published: 'Xuất bản', draft: 'Nháp', closed: 'Đóng' };

  if (loading) return <div className="text-center py-10">Đang tải...</div>;

  return (
    <div>
      <div className="flex justify-between items-start mb-4 gap-3">
        <div>
          <h1 className="text-xl sm:text-2xl font-bold text-gray-800">📝 Quản lý bài thi</h1>
          <p className="text-xs text-gray-500 mt-0.5">Quản lý và tổ chức các bài kiểm tra</p>
        </div>
        <button onClick={() => openModal()}
          className="flex-shrink-0 flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
          </svg>
          Tạo
        </button>
      </div>

      {/* Filter */}
      <div className="bg-white rounded-lg shadow p-3 mb-4">
        <label className="block text-xs font-medium text-gray-600 mb-1">Lọc theo môn học</label>
        <div className="flex gap-2">
          <select value={selectedSubjectId} onChange={e => setSelectedSubjectId(e.target.value)}
            className="flex-1 px-2 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Tất cả</option>
            {subjects.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
          </select>
          {selectedSubjectId && (
            <button onClick={() => setSelectedSubjectId('')}
              className="px-3 py-2 bg-gray-200 rounded-lg text-sm hover:bg-gray-300">
              Xóa
            </button>
          )}
        </div>
      </div>

      {exams.length === 0 ? (
        <div className="bg-white rounded-lg shadow p-10 text-center text-gray-500 text-sm">
          Chưa có bài thi nào
        </div>
      ) : (
        <>
          {/* Desktop table (sm+) */}
          <div className="hidden sm:block bg-white rounded-lg shadow overflow-x-auto">
            <table className="w-full">
              <thead className="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                  {['Tiêu đề', 'Môn học', 'TG', 'Lần thi', 'Câu', 'Trạng thái', ''].map(h => (
                    <th key={h} className="px-4 py-3 text-left font-medium">{h}</th>
                  ))}
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {exams.map(exam => (
                  <tr key={exam.id} className="hover:bg-gray-50 text-sm">
                    <td className="px-4 py-3 font-medium text-gray-800">{exam.title}</td>
                    <td className="px-4 py-3 text-gray-500">{exam.subject?.name}</td>
                    <td className="px-4 py-3 text-gray-500">{exam.duration}p</td>
                    <td className="px-4 py-3 text-gray-500">{exam.max_attempts || 1}</td>
                    <td className="px-4 py-3 text-gray-500">{exam.total_questions || 0}</td>
                    <td className="px-4 py-3">
                      <select value={exam.status} onChange={e => handleStatusChange(exam.id, e.target.value)}
                        className="border rounded px-2 py-1 text-xs">
                        <option value="draft">Nháp</option>
                        <option value="published">Xuất bản</option>
                        <option value="closed">Đóng</option>
                      </select>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex gap-1.5">
                        <button onClick={() => navigate(`/teacher/questions?exam_id=${exam.id}`)}
                          className="px-2 py-1 bg-purple-600 text-white rounded text-xs hover:bg-purple-700">Câu hỏi</button>
                        <button onClick={() => openModal(exam)}
                          className="px-2 py-1 bg-amber-500 text-white rounded text-xs hover:bg-amber-600">Sửa</button>
                        <button onClick={() => handleDelete(exam.id, exam.title)}
                          className="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">Xóa</button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Mobile card list (< sm) */}
          <div className="sm:hidden space-y-3">
            {exams.map(exam => (
              <div key={exam.id} className="bg-white rounded-lg shadow p-4">
                <div className="flex justify-between items-start mb-2 gap-2">
                  <div className="min-w-0">
                    <p className="font-semibold text-sm text-gray-800 leading-snug">{exam.title}</p>
                    <p className="text-xs text-gray-500 mt-0.5">{exam.subject?.name}</p>
                  </div>
                  <span className={`text-xs px-2 py-0.5 rounded-full flex-shrink-0 ${statusStyle[exam.status]}`}>
                    {statusLabel[exam.status]}
                  </span>
                </div>
                {/* Meta row */}
                <div className="flex gap-3 text-xs text-gray-400 mb-3">
                  <span>⏱ {exam.duration}p</span>
                  <span>🔄 {exam.max_attempts || 1} lần</span>
                  <span>❓ {exam.total_questions || 0} câu</span>
                </div>
                {/* Status select */}
                <select value={exam.status} onChange={e => handleStatusChange(exam.id, e.target.value)}
                  className="w-full border rounded-lg px-2 py-2 text-xs mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                  <option value="draft">Nháp</option>
                  <option value="published">Xuất bản</option>
                  <option value="closed">Đóng</option>
                </select>
                {/* Action buttons */}
                <div className="grid grid-cols-3 gap-2">
                  <button onClick={() => navigate(`/teacher/questions?exam_id=${exam.id}`)}
                    className="py-2 bg-purple-600 text-white rounded text-xs hover:bg-purple-700">Câu hỏi</button>
                  <button onClick={() => openModal(exam)}
                    className="py-2 bg-amber-500 text-white rounded text-xs hover:bg-amber-600">Sửa</button>
                  <button onClick={() => handleDelete(exam.id, exam.title)}
                    className="py-2 bg-red-500 text-white rounded text-xs hover:bg-red-600">Xóa</button>
                </div>
              </div>
            ))}
          </div>
        </>
      )}

      {/* Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-black/50 flex items-end sm:items-center justify-center z-50">
          <div className="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-xl p-5 max-h-[90vh] overflow-y-auto">
            <div className="sm:hidden w-10 h-1 bg-gray-300 rounded-full mx-auto mb-4" />
            <h2 className="text-lg font-bold mb-4">{editingExam ? 'Sửa bài thi' : 'Tạo bài thi'}</h2>
            <form onSubmit={handleSubmit} className="space-y-3">
              <input type="text" placeholder="Tiêu đề"
                className="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={formData.title} onChange={e => setFormData({ ...formData, title: e.target.value })} required />
              <select className="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={formData.subject_id} onChange={e => setFormData({ ...formData, subject_id: e.target.value })} required>
                <option value="">Chọn môn học</option>
                {subjects.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
              </select>
              <input type="number" placeholder="Thời gian (phút)"
                className="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={formData.duration} onChange={e => setFormData({ ...formData, duration: parseInt(e.target.value) || 30 })} min="1" required />
              <div>
                <label className="block text-xs font-medium text-gray-600 mb-1">Số lần thi tối đa</label>
                <input type="number"
                  className="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  value={formData.max_attempts}
                  onChange={e => { let n = parseInt(e.target.value); if (isNaN(n)) n = 1; setFormData({ ...formData, max_attempts: Math.min(10, Math.max(1, n)) }); }}
                  min="1" max="10" required />
              </div>
              <select className="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={formData.status} onChange={e => setFormData({ ...formData, status: e.target.value })}>
                <option value="draft">Nháp</option>
                <option value="published">Xuất bản</option>
                <option value="closed">Đã đóng</option>
              </select>
              <div className="flex gap-2 pt-1">
                <button type="submit"
                  className="flex-1 bg-blue-600 text-white py-2.5 rounded-lg text-sm hover:bg-blue-700">
                  {editingExam ? 'Cập nhật' : 'Tạo'}
                </button>
                <button type="button" onClick={() => setShowModal(false)}
                  className="flex-1 bg-gray-200 text-gray-700 py-2.5 rounded-lg text-sm hover:bg-gray-300">
                  Hủy
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default ExamManager;