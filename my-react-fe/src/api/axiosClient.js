// api/axiosClient.js
import axios from "axios";

const axiosClient = axios.create({
  // baseURL: "http://localhost:8000/api",
  baseURL: `${import.meta.env.VITE_API_URL}/api`,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  withCredentials: true,
});

// ==================== AUTO LOGOUT IDLE ====================
let lastActivity = Date.now();
let idleInterval = null;
const IDLE_TIMEOUT = 30 * 60 * 1000; // 30 phút

const resetActivity = () => {
  lastActivity = Date.now();
};

// Hàm chuyển hướng đến login
const redirectToLogin = () => {
  // Lấy đường dẫn hiện tại (bao gồm cả subpath nếu có)
  const currentPath = window.location.pathname;
  const loginPath = "/login";
  
  // Nếu đã ở trang login thì không redirect
  if (currentPath === loginPath || currentPath === "/" + loginPath) {
    return;
  }
  
  // Sử dụng window.location.href với đường dẫn tương đối
  // Cách này sẽ giữ nguyên subpath
  window.location.href = loginPath;
};

const checkIdleAndLogout = () => {
  const token = localStorage.getItem("token");
  if (!token) return;
  
  const now = Date.now();
  const idleTime = now - lastActivity;
  
  if (idleTime >= IDLE_TIMEOUT) {
    console.log(`Không hoạt động ${Math.floor(idleTime / 60000)} phút, đăng xuất!`);
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    localStorage.removeItem("role");
<<<<<<< HEAD
    redirectToLogin();
=======
    window.location.replace("/#/login");
>>>>>>> 583473b (update timeout axiosClient+ app.jsx BrowserRouter  sang HashRouter)
  }
};

const events = ["click", "mousemove", "keydown", "scroll", "touchstart", "mousedown"];
events.forEach(event => window.addEventListener(event, resetActivity));
idleInterval = setInterval(checkIdleAndLogout, 10000);
// ==================== KẾT THÚC ====================

// ✅ Request interceptor: Tự động thêm token vào header
axiosClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// ✅ Response interceptor xử lý lỗi 401 và 403
axiosClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem("token");
      localStorage.removeItem("role");
<<<<<<< HEAD
      redirectToLogin();
=======
      window.location.replace("/#/login");
>>>>>>> 583473b (update timeout axiosClient+ app.jsx BrowserRouter  sang HashRouter)
    }
    
    // Xử lý lỗi 403
    if (error.response?.status === 403) {
      console.error("Forbidden:", error.response?.data?.message || "Bạn không có quyền thực hiện hành động này");
    }
    
    return Promise.reject(error);
  }
);

export default axiosClient;
