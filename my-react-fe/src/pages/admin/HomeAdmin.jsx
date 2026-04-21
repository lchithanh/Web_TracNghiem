// src/pages/admin/HomeAdmin.jsx
import React, { useState } from "react";
import { Outlet, NavLink, useNavigate } from "react-router-dom";
import { useAuth } from "../../context/AuthContext";

const HomeAdmin = () => {
  const navigate = useNavigate();
  const { user, logout } = useAuth();
  const [drawerOpen, setDrawerOpen] = useState(false);

  const menuItems = [
    { path: "/admin", label: "🏠 Tổng quan" },
    { path: "/admin/users", label: "👥 Quản lý người dùng" },
    { path: "/admin/teachers", label: "👨‍🏫 Quản lý giảng viên" }, // NEW
    { path: "/admin/subjects", label: "📚 Quản lý môn học" },
    { path: "/admin/exams", label: "📝 Quản lý bài thi" },
    { path: "/admin/classes", label: "👥 Quản lý lớp học" },
  ];

  const handleLogout = async () => {
    await logout();
    navigate("/login");
  };

  return (
    <div className="flex min-h-screen bg-gray-100">
      {/* Overlay — mobile only */}
      {drawerOpen && (
        <div
          className="fixed inset-0 z-30 bg-slate-900/50 sm:hidden"
          onClick={() => setDrawerOpen(false)}
        />
      )}

      {/* Sidebar */}
      <aside
        className={[
          "fixed top-0 left-0 h-full z-40 w-64 bg-white shadow-lg flex flex-col",
          "transition-transform duration-200 ease-in-out",
          drawerOpen ? "translate-x-0" : "-translate-x-full",
          "sm:relative sm:translate-x-0",
        ].join(" ")}
      >
        <div className="flex items-center justify-between flex-shrink-0 p-5 border-b">
          <div>
            <h2 className="text-xl font-bold text-blue-600">QuizPro</h2>
            <p className="text-xs text-gray-500 mt-0.5">Quản trị hệ thống</p>
          </div>
          <button
            onClick={() => setDrawerOpen(false)}
            className="p-1 text-gray-400 rounded sm:hidden hover:text-gray-600 hover:bg-gray-100"
          >
            <svg
              className="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <nav className="flex-1 p-3 space-y-0.5 overflow-y-auto">
          {menuItems.map((item) => (
            <NavLink
              key={item.path}
              to={item.path}
              end={item.path === "/admin"}
              onClick={() => setDrawerOpen(false)}
              className={({ isActive }) =>
                `block px-4 py-2.5 rounded-lg text-sm transition-colors ${
                  isActive
                    ? "bg-blue-600 text-white font-medium"
                    : "text-gray-700 hover:bg-gray-100"
                }`
              }
            >
              {item.label}
            </NavLink>
          ))}
        </nav>

        <div className="flex-shrink-0 p-4 border-t">
          <div className="flex items-center gap-3 mb-3">
            <div className="flex items-center justify-center flex-shrink-0 bg-blue-600 rounded-full w-9 h-9">
              <span className="text-sm font-bold text-white">
                {user?.name?.charAt(0)?.toUpperCase() || "A"}
              </span>
            </div>
            <div className="min-w-0">
              <p className="text-sm font-medium truncate">
                {user?.name || "Admin"}
              </p>
              <p className="text-xs text-gray-500 truncate">{user?.email}</p>
            </div>
          </div>
          <button
            onClick={handleLogout}
            className="w-full py-2 text-sm text-white transition bg-red-600 rounded-lg hover:bg-red-700"
          >
            Đăng xuất
          </button>
        </div>
      </aside>

      {/* Main content */}
      <div className="flex flex-col flex-1 min-w-0">
        {/* Mobile topbar */}
        <header className="sticky top-0 z-20 flex items-center gap-3 px-4 py-3 bg-white shadow-sm sm:hidden">
          <button
            onClick={() => setDrawerOpen(true)}
            className="p-1.5 -ml-1 rounded-md text-gray-500 hover:bg-gray-100"
            aria-label="Mở menu"
          >
            <svg
              className="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
          <span className="font-bold text-blue-600">QuizPro</span>
        </header>

        <main className="flex-1 p-4 overflow-auto sm:p-8">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default HomeAdmin;
