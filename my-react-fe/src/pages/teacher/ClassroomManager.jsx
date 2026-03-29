// src/pages/teacher/ClassroomManager.jsx
import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axiosClient from '../../api/axiosClient';

const ClassroomManager = () => {
  const navigate = useNavigate();
  const [classes, setClasses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [showInviteModal, setShowInviteModal] = useState(false);
  const [editingClass, setEditingClass] = useState(null);
  const [selectedClass, setSelectedClass] = useState(null);
  const [inviteInfo, setInviteInfo] = useState(null);
  const [formData, setFormData] = useState({ name: '', description: '' });
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    fetchClasses();
  }, []);

  const fetchClasses = async () => {
    try {
      const res = await axiosClient.get('/classrooms');
      setClasses(res.data?.data || []);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const openModal = (cls = null) => {
    if (cls) {
      setEditingClass(cls);
      setFormData({ name: cls.name, description: cls.description || '' });
    } else {
      setEditingClass(null);
      setFormData({ name: '', description: '' });
    }
    setShowModal(true);
  };

  const loadInviteCode = async (cls) => {
    try {
      console.log('Loading invite code for class:', cls.id);
      const res = await axiosClient.get(`/classrooms/${cls.id}/invite-code`);
      console.log('Invite code response:', res.data);
      
      if (res.data?.data?.invite_code) {
        setInviteInfo({
          invite_code: res.data.data.invite_code,
          expires_at: res.data.data.expires_at,
          is_valid: res.data.data.is_valid
        });
        setSelectedClass(cls);
        setShowInviteModal(true);
      } else {
        alert('Không thể lấy mã mời');
      }
    } catch (err) {
      console.error('Error loading invite code:', err);
      alert(err.response?.data?.message || 'Lỗi tải mã mời');
    }
  };

  const regenerateCode = async () => {
    try {
      const res = await axiosClient.post(`/classrooms/${selectedClass.id}/regenerate-code`);
      console.log('Regenerate response:', res.data);
      
      if (res.data?.data?.invite_code) {
        setInviteInfo({
          invite_code: res.data.data.invite_code,
          expires_at: res.data.data.expires_at,
          is_valid: res.data.data.is_valid
        });
        alert('Tạo mã mới thành công');
      } else {
        alert('Không thể tạo mã mới');
      }
    } catch (err) {
      console.error('Error regenerating code:', err);
      alert(err.response?.data?.message || 'Lỗi tạo mã mới');
    }
  };

  const copyToClipboard = (text) => {
    if (text) {
      navigator.clipboard.writeText(text);
      alert('Đã sao chép mã: ' + text);
    } else {
      alert('Không có mã để sao chép');
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      if (editingClass) {
        await axiosClient.put(`/classrooms/${editingClass.id}`, formData);
        alert('Cập nhật thành công');
      } else {
        await axiosClient.post('/classrooms', formData);
        alert('Tạo lớp thành công');
      }
      setShowModal(false);
      fetchClasses();
    } catch (err) {
      alert(err.response?.data?.message || 'Lỗi');
    } finally {
      setSubmitting(false);
    }
  };

  const handleDelete = async (id, name) => {
    if (!window.confirm(`Xóa lớp "${name}"?`)) return;
    try {
      await axiosClient.delete(`/classrooms/${id}`);
      fetchClasses();
      alert('Xóa thành công');
    } catch (err) {
      alert(err.response?.data?.message || 'Lỗi xóa');
    }
  };

  if (loading) {
    return <div className="text-center py-10">Đang tải...</div>;
  }

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Quản lý lớp học</h1>
        <button
          onClick={() => openModal()}
          className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          + Tạo lớp
        </button>
      </div>

      {classes.length === 0 ? (
        <div className="bg-white rounded shadow p-12 text-center text-gray-500">
          Chưa có lớp học nào
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {classes.map(cls => (
            <div key={cls.id} className="bg-white rounded shadow p-4 flex flex-col">
              {/* Thông tin bên trên */}
              <div className="flex-1">
                <h3 className="font-semibold text-lg">{cls.name}</h3>
                {cls.description && (
                  <p className="text-gray-500 text-sm mt-1">{cls.description}</p>
                )}
                <p className="text-xs text-gray-400 mt-2">
                  Số học sinh: {cls.students?.length || 0}
                </p>
              </div>
              
              {/* Các nút bên dưới */}
              <div className="flex gap-2 mt-4 pt-3 border-t border-gray-200">
                <button
                  onClick={() => navigate(`/teacher/classes/${cls.id}`)}
                  className="flex-1 bg-blue-600 text-white py-1 rounded text-sm hover:bg-blue-700 transition-colors"
                >
                  Chi tiết
                </button>
                <button
                  onClick={() => loadInviteCode(cls)}
                  className="flex-1 bg-green-600 text-white py-1 rounded text-sm hover:bg-green-700 transition-colors"
                >
                  Mã mời
                </button>
                <button
                  onClick={() => openModal(cls)}
                  className="flex-1 bg-yellow-500 text-white py-1 rounded text-sm hover:bg-yellow-600 transition-colors"
                >
                  Sửa
                </button>
                <button
                  onClick={() => handleDelete(cls.id, cls.name)}
                  className="flex-1 bg-red-500 text-white py-1 rounded text-sm hover:bg-red-600 transition-colors"
                >
                  Xóa
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Modal tạo/sửa lớp */}
      {showModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded p-6 w-full max-w-md">
            <h2 className="text-xl font-bold mb-4">
              {editingClass ? 'Sửa lớp học' : 'Tạo lớp học mới'}
            </h2>
            <form onSubmit={handleSubmit}>
              <div className="mb-3">
                <input
                  type="text"
                  placeholder="Tên lớp"
                  className="w-full p-2 border rounded"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  required
                />
              </div>
              <div className="mb-4">
                <textarea
                  placeholder="Mô tả"
                  className="w-full p-2 border rounded"
                  rows="3"
                  value={formData.description}
                  onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                />
              </div>
              <div className="flex gap-3">
                <button
                  type="submit"
                  disabled={submitting}
                  className="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
                >
                  {submitting ? 'Đang lưu...' : editingClass ? 'Cập nhật' : 'Tạo'}
                </button>
                <button
                  type="button"
                  onClick={() => setShowModal(false)}
                  className="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                >
                  Hủy
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal mã mời */}
      {showInviteModal && selectedClass && inviteInfo && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded p-6 w-full max-w-md">
            <h2 className="text-xl font-bold mb-2">Mã mời lớp</h2>
            <p className="text-gray-600 mb-4">{selectedClass.name}</p>
            
            <div className="text-center mb-4">
              <div className="bg-gray-100 p-4 rounded-lg border-2 border-dashed border-gray-300">
                <p className="text-3xl font-mono font-bold tracking-wider text-blue-600">
                  {inviteInfo.invite_code || 'Chưa có mã'}
                </p>
              </div>
              {inviteInfo.invite_code && (
                <button
                  onClick={() => copyToClipboard(inviteInfo.invite_code)}
                  className="mt-2 text-blue-600 text-sm hover:text-blue-800"
                >
                  📋 Sao chép mã
                </button>
              )}
            </div>
            
            <div className="mb-4 text-sm text-gray-500">
              <p>⏰ Hết hạn: {inviteInfo.expires_at ? new Date(inviteInfo.expires_at).toLocaleDateString('vi-VN') : 'Không giới hạn'}</p>
              <p className="mt-1 text-xs">* Học sinh nhập mã này để tham gia lớp</p>
            </div>
            
            <div className="flex gap-3">
              <button
                onClick={regenerateCode}
                className="flex-1 bg-yellow-600 text-white py-2 rounded hover:bg-yellow-700"
              >
                Tạo mã mới
              </button>
              <button
                onClick={() => setShowInviteModal(false)}
                className="flex-1 bg-gray-300 py-2 rounded hover:bg-gray-400"
              >
                Đóng
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default ClassroomManager;