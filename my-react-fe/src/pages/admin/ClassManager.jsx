// src/pages/teacher/ClassManager.jsx
import React, { useEffect, useState } from 'react';
import axiosClient from '../../api/axiosClient';

const ClassManager = () => {
  const [classrooms, setClassrooms] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchName, setSearchName] = useState('');

  useEffect(() => {
    fetchClassrooms();
  }, []);

  const fetchClassrooms = async () => {
    try {
      const response = await axiosClient.get('/classrooms');
      const data = response.data?.data || response.data || [];
      setClassrooms(data);
    } catch (error) {
      console.error('Lỗi tải lớp học:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id, name) => {
    if (!window.confirm(`Giải tán lớp "${name}"?\n\nHành động này sẽ xóa lớp và gỡ tất cả học sinh, bài thi khỏi lớp.`)) return;
    try {
      const response = await axiosClient.delete(`/classrooms/${id}`);
      alert(response.data.message || 'Đã giải tán lớp thành công!');
      fetchClassrooms();
    } catch (error) {
      console.error('Lỗi chi tiết:', error.response?.data);
      alert('Lỗi: ' + (error.response?.data?.message || error.message));
    }
  };

  const copyInviteCode = (code) => {
    navigator.clipboard.writeText(code);
    alert('Đã sao chép mã mời: ' + code);
  };

  // Lọc theo tên lớp
  const filteredClassrooms = classrooms.filter(c =>
    c.name?.toLowerCase().includes(searchName.toLowerCase())
  );

  if (loading) return <div className="text-center py-10">Đang tải...</div>;

  return (
    <div>
      {/* Header + tìm kiếm */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 className="text-2xl font-bold text-gray-800">🏫 Quản lý lớp học</h1>
        <div className="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
          <input
            type="text"
            placeholder="🔍 Tìm theo tên lớp..."
            value={searchName}
            onChange={(e) => setSearchName(e.target.value)}
            className="border rounded px-3 py-2 text-sm w-full sm:w-64"
          />
          <button className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tạo lớp mới
          </button>
        </div>
      </div>

      {/* Danh sách lớp */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredClassrooms.map(classroom => {
          const studentCount = classroom.students?.length || 0;
          const examCount = classroom.exams?.length || 0;

          return (
            <div key={classroom.id} className="bg-white rounded-lg shadow-md overflow-hidden">
              <div className="p-4 border-b">
                <h3 className="text-lg font-semibold text-gray-800">{classroom.name}</h3>
                {classroom.description && (
                  <p className="text-sm text-gray-500 mt-1">{classroom.description}</p>
                )}
              </div>

              <div className="p-4 space-y-2">
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">👨‍🏫 Giảng viên:</span>
                  <span className="text-gray-700">{classroom.teacher?.name || '—'}</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">👨‍🎓 Sinh viên:</span>
                  <span className="text-gray-700">{studentCount} sinh viên</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">📝 Bài thi:</span>
                  <span className={`${examCount > 0 ? 'text-green-600 font-medium' : 'text-gray-700'}`}>
                    {examCount} bài thi
                  </span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">🔑 Mã mời:</span>
                  <div className="flex items-center gap-2">
                    <code className="bg-gray-100 px-2 py-1 rounded text-sm">{classroom.invite_code}</code>
                    <button
                      onClick={() => copyInviteCode(classroom.invite_code)}
                      className="text-blue-500 hover:text-blue-700 text-xs"
                    >
                      Sao chép
                    </button>
                  </div>
                </div>
              </div>

              <div className="bg-gray-50 px-4 py-3 flex justify-end gap-2">
                <button className="text-blue-600 hover:text-blue-800 text-sm">
                  Sửa
                </button>
                <button
                  onClick={() => handleDelete(classroom.id, classroom.name)}
                  className="text-red-600 hover:text-red-800 text-sm"
                >
                  Giải tán
                </button>
              </div>
            </div>
          );
        })}
      </div>

      {filteredClassrooms.length === 0 && (
        <div className="bg-white rounded shadow p-12 text-center text-gray-500">
          {searchName ? 'Không tìm thấy lớp học' : 'Chưa có lớp học nào'}
        </div>
      )}
    </div>
  );
};

export default ClassManager;