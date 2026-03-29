// src/pages/teacher/SubjectManager.jsx
import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axiosClient from '../../api/axiosClient';

const SubjectManager = () => {
  const navigate = useNavigate();
  const [subjects, setSubjects] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editingSubject, setEditingSubject] = useState(null);
  const [formData, setFormData] = useState({ name: '', description: '' });

  useEffect(() => { fetchSubjects(); }, []);

  const fetchSubjects = async () => {
    try {
      const res = await axiosClient.get('/subjects');
      setSubjects(res.data?.data || res.data || []);
    } catch (error) { console.error(error); }
    finally { setLoading(false); }
  };

  const openModal = (subject = null) => {
    setEditingSubject(subject);
    setFormData({ name: subject?.name || '', description: subject?.description || '' });
    setShowModal(true);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingSubject) {
        await axiosClient.put(`/subjects/${editingSubject.id}`, formData);
        alert('Cập nhật thành công');
      } else {
        await axiosClient.post('/subjects', formData);
        alert('Thêm môn học thành công');
      }
      setShowModal(false);
      fetchSubjects();
    } catch (error) { alert(error.response?.data?.message || 'Lỗi'); }
  };

  const handleDelete = async (id, name) => {
    if (window.confirm(`Xóa môn học "${name}"?`)) {
      await axiosClient.delete(`/subjects/${id}`);
      fetchSubjects();
    }
  };

  if (loading) return <div className="text-center py-10">Đang tải...</div>;

  return (
    <div>
      <div className="flex justify-between items-center mb-4">
        <h1 className="text-xl sm:text-2xl font-bold text-gray-800">📚 Quản lý môn học</h1>
        <button onClick={() => openModal()}
          className="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 text-sm">
          + Thêm
        </button>
      </div>

      {/* 1 col on mobile, 2 on sm, 3 on lg */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        {subjects.map(sub => (
          <div key={sub.id} className="bg-white rounded-lg shadow p-4">
            <h3 className="font-semibold text-gray-800 truncate">{sub.name}</h3>
            <p className="text-gray-500 text-sm mt-1 line-clamp-2">
              {sub.description || 'Không có mô tả'}
            </p>
            <div className="flex gap-2 mt-3">
              <button onClick={() => navigate(`/teacher/exams?subject_id=${sub.id}`)}
                className="flex-1 bg-blue-600 text-white py-1.5 rounded text-xs hover:bg-blue-700">
                Bài thi
              </button>
              <button onClick={() => openModal(sub)}
                className="flex-1 bg-yellow-500 text-white py-1.5 rounded text-xs hover:bg-yellow-600">
                Sửa
              </button>
              <button onClick={() => handleDelete(sub.id, sub.name)}
                className="flex-1 bg-red-500 text-white py-1.5 rounded text-xs hover:bg-red-600">
                Xóa
              </button>
            </div>
          </div>
        ))}
      </div>

      {/* Bottom sheet modal on mobile, centered on sm+ */}
      {showModal && (
        <div className="fixed inset-0 bg-black/50 flex items-end sm:items-center justify-center z-50">
          <div className="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-xl p-5">
            {/* drag handle on mobile */}
            <div className="sm:hidden w-10 h-1 bg-gray-300 rounded-full mx-auto mb-4" />
            <h2 className="text-lg font-bold mb-4">
              {editingSubject ? 'Sửa môn học' : 'Thêm môn học'}
            </h2>
            <form onSubmit={handleSubmit} className="space-y-3">
              <input type="text" placeholder="Tên môn học"
                className="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} required />
              <textarea placeholder="Mô tả" rows="3"
                className="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={formData.description} onChange={e => setFormData({ ...formData, description: e.target.value })} />
              <div className="flex gap-2 pt-1">
                <button type="submit"
                  className="flex-1 bg-blue-600 text-white py-2.5 rounded-lg text-sm hover:bg-blue-700">
                  {editingSubject ? 'Cập nhật' : 'Thêm'}
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

export default SubjectManager;