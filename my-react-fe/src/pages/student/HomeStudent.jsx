// src/pages/student/HomeStudent.jsx
import React, { useState } from "react";
import { Outlet, NavLink, useNavigate } from "react-router-dom";
import { useAuth } from "../../context/AuthContext";

const HomeStudent = () => {
  const navigate = useNavigate();
  const { user, logout } = useAuth();
  const [drawerOpen, setDrawerOpen] = useState(false);

  const menuItems = [
    { path: "/student", label: "🏠 Trang chủ" },
    { path: "/student/subjects", label: "📚 Môn học" },
    { path: "/student/exams", label: "📝 Bài thi" },
    { path: "/student/attempts", label: "📊 Lịch sử làm bài" },
    { path: "/student/classes", label: "👥 Lớp học" },
    { path: "/student/join", label: "🔑 Tham gia lớp" },
  ];

  const handleLogout = async () => {
    await logout();
    navigate("/login");
  };

  return (
    <div className="flex min-h-screen bg-slate-100">

      {/* Sidebar */}
      <aside
        className={[
          "fixed top-0 left-0 h-full z-40 w-64 bg-gradient-to-b from-slate-800 to-slate-900 shadow-lg flex flex-col",
          "transition-transform duration-200 ease-in-out",
          drawerOpen ? "translate-x-0" : "-translate-x-full",
          "sm:relative sm:translate-x-0",
        ].join(" ")}
      >
        {/* Logo */}
        <div className="p-6 border-b border-slate-700 flex items-center justify-between flex-shrink-0">
          <div>
            <h2 className="text-xl font-bold text-white">QuizPro</h2>
            <p className="text-sm text-slate-400 mt-1">Học sinh</p>
          </div>

          <button
            onClick={() => setDrawerOpen(false)}
            className="sm:hidden p-1 rounded text-slate-300 hover:text-white hover:bg-slate-700"
          >
            ✕
          </button>
        </div>

        {/* Menu */}
        <nav className="flex-1 p-4 space-y-2 overflow-y-auto">
          {menuItems.map((item) => (
            <NavLink
              key={item.path}
              to={item.path}
              end={item.path === "/student"}
              onClick={() => setDrawerOpen(false)}
              className={({ isActive }) =>
                `block px-4 py-2 rounded-lg transition ${
                  isActive
                    ? "bg-white text-slate-800 font-semibold shadow"
                    : "text-slate-300 hover:bg-slate-700 hover:text-white"
                }`
              }
            >
              {item.label}
            </NavLink>
          ))}
        </nav>

        {/* User */}
        <div className="p-4 border-t border-slate-700">
          <div className="flex items-center gap-3 mb-3">
            <div className="w-10 h-10 bg-white rounded-full flex items-center justify-center">
              <span className="text-slate-700 font-bold">
                {user?.name?.charAt(0)?.toUpperCase() || "S"}
              </span>
            </div>

            <div className="min-w-0">
              <p className="text-white font-medium text-sm truncate">
                {user?.name || "Học sinh"}
              </p>
              <p className="text-slate-400 text-xs truncate">{user?.email}</p>
            </div>
          </div>

          <button
            onClick={handleLogout}
            className="w-full py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm"
          >
            Đăng xuất
          </button>
        </div>
      </aside>

      {/* Main */}
      <div className="flex-1 flex flex-col min-w-0">

        {/* Mobile topbar */}
        <header className="sm:hidden sticky top-0 z-20 bg-white shadow-sm flex items-center gap-3 px-4 py-3">
          <button
            onClick={() => setDrawerOpen(true)}
            className="p-1.5 -ml-1 rounded-md text-gray-500 hover:bg-gray-100"
          >
            ☰
          </button>
          <span className="font-bold text-slate-800">QuizPro</span>
        </header>

        <main className="flex-1 p-4 sm:p-8 overflow-auto">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default HomeStudent;