\# Hệ thống Quản lý Thi Trắc Nghiệm Online



\## 📋 Giới thiệu



Hệ thống quản lý thi trắc nghiệm online được xây dựng với ReactJS và TailwindCSS, cung cấp đầy đủ các chức năng cho 3 vai trò: \*\*Quản trị viên\*\*, \*\*Giảng viên\*\* và \*\*Sinh viên\*\*.



\## 🚀 Tính năng chính



\### 👑 Quản trị viên

\- 📊 \*\*Dashboard tổng quan\*\* - Thống kê số lượng người dùng, bài thi, môn học, lớp học

\- 👥 \*\*Quản lý người dùng\*\* - Thêm, sửa, xóa tài khoản (Admin, Giảng viên, Sinh viên)

\- 📚 \*\*Quản lý môn học\*\* - Tạo, cập nhật, xóa môn học

\- 📝 \*\*Quản lý bài thi\*\* - Xem và quản lý tất cả bài thi trong hệ thống

\- 👨‍🏫 \*\*Quản lý lớp học\*\* - Xem và quản lý tất cả lớp học



\### 👨‍🏫 Giảng viên

\- 📚 \*\*Quản lý môn học được phân công\*\*

\- 📝 \*\*Tạo và quản lý bài thi\*\* (trắc nghiệm, có thời gian, số câu hỏi)

\- 👥 \*\*Quản lý lớp học\*\* - Tạo lớp, thêm/xóa sinh viên

\- 📊 \*\*Xem kết quả thi của sinh viên\*\*

\- 📈 \*\*Thống kê điểm số\*\*



\### 👨‍🎓 Sinh viên

\- 🏠 \*\*Dashboard cá nhân\*\* - Xem bài thi sắp tới, đã tham gia

\- 📝 \*\*Làm bài thi trực tuyến\*\*

\- ⏱️ \*\*Tính năng đếm giờ\*\* khi làm bài

\- 📊 \*\*Xem kết quả và điểm số\*\*

\- 📜 \*\*Lịch sử làm bài\*\*



\## 🛠️ Công nghệ sử dụng



\### Frontend

\- \*\*React 18\*\* - Thư viện UI

\- \*\*React Router DOM v6\*\* - Điều hướng

\- \*\*Axios\*\* - Gọi API

\- \*\*TailwindCSS\*\* - Styling và Responsive

\- \*\*Vite\*\* - Build tool



\### Backend (Giả định)

\- RESTful API

\- JWT Authentication



\## 📁 Cấu trúc thư mục

src/

├── api/

│ └── axiosClient.js # Cấu hình axios

├── components/

│ └── common/

│ ├── Layout.jsx # Layout chính

│ ├── Header.jsx # Header component

│ ├── Sidebar.jsx # Sidebar navigation

│ └── ProtectedRoute.jsx # Bảo vệ route

├── pages/

│ ├── login/

│ │ └── Login.jsx # Trang đăng nhập

│ ├── admin/

│ │ ├── DashboardAdmin.jsx # Dashboard Admin

│ │ ├── UserManager.jsx # Quản lý người dùng

│ │ ├── SubjectManager.jsx # Quản lý môn học

│ │ ├── ExamManager.jsx # Quản lý bài thi

│ │ └── ClassManager.jsx # Quản lý lớp học

│ ├── teacher/

│ │ ├── DashboardTeacher.jsx

│ │ ├── SubjectManager.jsx

│ │ ├── ExamManager.jsx

│ │ ├── ClassManager.jsx

│ │ └── ResultsView.jsx

│ └── student/

│ ├── DashboardStudent.jsx

│ ├── TakeExam.jsx

│ ├── ExamResult.jsx

│ └── History.jsx

├── utils/

│ └── auth.js # Xử lý authentication

├── App.jsx

├── main.jsx

└── index.css



text



\## 💻 Cài đặt và chạy dự án



\### Yêu cầu

\- Node.js 16.x hoặc cao hơn

\- npm hoặc yarn



\### Các bước cài đặt



1\. \*\*Clone repository\*\*

```bash

git clone <repository-url>

cd project-name

Cài đặt dependencies



bash

npm install

\# hoặc

yarn install

Cấu hình environment variables

Tạo file .env trong thư mục gốc:



env

VITE\_API\_URL=http://localhost:8000/api

Chạy dự án



bash

npm run dev

\# hoặc

yarn dev

Build production



bash

npm run build

\# hoặc

yarn build

🔐 Authentication

Hệ thống sử dụng JWT Token được lưu trong localStorage:



javascript

// Cấu trúc user object

{

&#x20; id: number,

&#x20; name: string,

&#x20; email: string,

&#x20; role: 'admin' | 'teacher' | 'student',

&#x20; token: string

}

📱 Responsive Design

Hệ thống được thiết kế responsive với các breakpoints:



Mobile: < 768px



Tablet: 768px - 1024px



Desktop: > 1024px



Tính năng responsive:

Bảng dữ liệu chuyển thành card view trên mobile



Sidebar có thể thu gọn



Grid layout tự động điều chỉnh



Typography scale responsive



🎨 UI Components

Các component chính:

StatCard

jsx

<StatCard 

&#x20; title="Tổng người dùng"

&#x20; value={100}

&#x20; icon="👥"

&#x20; color="blue"

/>

DataTable

Hỗ trợ sorting, filtering, pagination



Responsive: table trên desktop, card trên mobile



Modal Form

Thêm/Sửa dữ liệu



Validation



Loading states



Pagination

Hiển thị số trang thông minh



Chọn số items mỗi trang



Responsive layout



🔄 API Integration

Các endpoint chính:

javascript

// Users

GET    /api/users

POST   /api/users

PUT    /api/users/:id

DELETE /api/users/:id

DELETE /api/users/:id/force



// Subjects

GET    /api/subjects

POST   /api/subjects

PUT    /api/subjects/:id

DELETE /api/subjects/:id



// Exams

GET    /api/exams

POST   /api/exams

PUT    /api/exams/:id

DELETE /api/exams/:id



// Classes

GET    /api/classes

POST   /api/classes

PUT    /api/classes/:id

DELETE /api/classes/:id

🧪 Testing

bash

\# Chạy tests

npm run test



\# Chạy tests với coverage

npm run test:coverage

📦 Deployment

Deploy lên Vercel

bash

npm install -g vercel

vercel

Deploy lên Netlify

bash

npm run build

\# Upload thư mục dist lên Netlify

🤝 Đóng góp

Fork repository



Tạo branch mới (git checkout -b feature/AmazingFeature)



Commit changes (git commit -m 'Add some AmazingFeature')



Push lên branch (git push origin feature/AmazingFeature)



Mở Pull Request



📄 License

Dự án được phân phối dưới giấy phép MIT. Xem file LICENSE để biết thêm chi tiết.



👥 Nhóm phát triển

Backend Developer - Xây dựng RESTful API



Frontend Developer - ReactJS, TailwindCSS



🆘 Hỗ trợ

Nếu bạn gặp vấn đề, hãy tạo issue trên GitHub hoặc liên hệ qua email.



📝 Ghi chú cho Developer

Các component đã hoàn thiện:

✅ UserManager.jsx



CRUD users



Phân trang



Responsive (Table/Card view)



Force delete với confirm



Bảo vệ xóa chính mình



✅ DashboardAdmin.jsx



Thống kê tổng quan



Hành động nhanh



Hiển thị người dùng và bài thi mới



Responsive grid layout



Các component cần phát triển tiếp:

🔄 SubjectManager.jsx



Quản lý môn học



Phân công giảng viên



🔄 ExamManager.jsx



Tạo/Sửa bài thi



Thêm câu hỏi trắc nghiệm



🔄 ClassManager.jsx



Quản lý lớp học



Thêm/xóa sinh viên



Best Practices đã áp dụng:

State Management - Sử dụng React hooks (useState, useEffect)



Performance - Memoization, lazy loading



Error Handling - Try-catch, user-friendly messages



Security - JWT token, role-based access



UX - Loading states, confirm dialogs, responsive design



Code Style - Consistent naming, component structure





