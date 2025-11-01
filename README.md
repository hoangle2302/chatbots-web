# 🧠 Thư Viện AI – Nền tảng Chat Đa Mô Hình Cho Mọi Nhu Cầu

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://www.php.net/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-yellow.svg)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![HTML5](https://img.shields.io/badge/HTML5-ready-orange.svg)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-modern-blue.svg)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> “Every model you need, in one place.” – Thư Viện AI mang tới trải nghiệm trò chuyện, sáng tạo nội dung và quản trị người dùng bằng hơn **500 mô hình AI** thuộc các nhà cung cấp hàng đầu (OpenAI, Anthropic, Google, Qwen, Midjourney, DeepSeek, Suno…).

---

## 📌 Mục lục

1. [Tổng quan](#-tổng-quan)
2. [Điểm nhấn tính năng](#-điểm-nhấn-tính-năng)
3. [Kiến trúc hệ thống](#-kiến-trúc-hệ-thống)
4. [Bộ công nghệ](#-bộ-công-nghệ)
5. [Hướng dẫn cài đặt & chạy](#-hướng-dẫn-cài-đặt--chạy)
6. [API & Tích hợp AI](#-api--tích-hợp-ai)
7. [Giao diện & Trải nghiệm](#-giao-diện--trải-nghiệm)
8. [Bảo mật & Hiệu năng](#-bảo-mật--hiệu-năng)
9. [Roadmap](#-roadmap)
10. [Đóng góp & Hỗ trợ](#-đóng-góp--hỗ-trợ)
11. [Thông tin dự án](#-thông-tin-dự-án)

---

## 🎯 Tổng quan

Thư Viện AI là một nền tảng web thuần **PHP + JavaScript** với mục tiêu:

- Cung cấp **trải nghiệm chat đa mô hình** (text, image, audio, video) chỉ qua một giao diện.
- Hỗ trợ **nhà quản trị** quản lý người dùng, credits, thống kê sử dụng.
- Cho phép **người dùng cuối** tiếp cận nhanh các mô hình AI mới nhất, kể cả khi không sở hữu API key đắt đỏ.
- Mang lại **kiến trúc gọn nhẹ**, dễ triển khai ở môi trường nội bộ hoặc demo.

---

## 🌟 Điểm nhấn tính năng

| Nhóm đối tượng | Tính năng chính |
|----------------|-----------------|
| **Người dùng** | Đăng ký/đăng nhập, quản lý credits, trò chuyện realtime, chọn nhanh model, ENSEMBLE mode, lưu lịch sử.
| **Quản trị viên** | Đăng nhập riêng, xem danh sách user, cộng/trừ/đặt credits, xem tổng hợp models, thống kê realtime.
| **AI Models** | 500+ mô hình từ GPT-4, Claude, Gemini, Qwen, Midjourney, Flux, Suno…; phân loại theo text/image/audio/video.
| **Tích hợp** | Key4U API (đa nhà cung cấp), Qwen API (miễn phí), dịch vụ nội bộ (chat-real, documents, models).
| **UI/UX** | Thiết kế glassmorphism, responsive, dark tone, animation nhẹ nhàng, hỗ trợ mobile & desktop.

### 🔑 Các điểm nổi bật mới nhất
- **Header Authorization linh hoạt**: tương thích Windows, macOS, Docker, PHP built-in server → tránh lỗi 401/403.
- **Bộ lọc model nâng cao**: tìm kiếm theo từ khoá, nhà cung cấp, loại dữ liệu; hiển thị số lượng realtime.
- **Dashboard admin hiện đại**: widget thống kê, modal credits, bảng người dùng cố định, grid mô hình.
- **Script hỗ trợ triển khai**: `setup-database`, `start.bat` (Windows) / `setup-database.sh` (Linux/macOS).

---

## 🧱 Kiến trúc hệ thống

```
chatbots-web
├── data/                      # Uploads, database tạm
├── src/
│   ├── php-backend/           # API PHP thuần
│   │   ├── api/               # auth.php, admin.php, chat-real.php, documents.php...
│   │   ├── models/            # User, Document, Log, AIQueryHistory
│   │   ├── services/          # Key4UService, QwenService, AIService, DocumentService
│   │   └── middleware/        # AuthMiddleware (JWT)
│   └── web/                   # Frontend HTML/CSS/JS
│       ├── index.html         # Trang chat chính
│       ├── admin-dashboard.html / admin-login.html
│       ├── login.html / register.html
│       ├── script-backend.js / style.css / config.js
└── start.bat / setup-database.*
```

- **Frontend** chạy ở `127.0.0.1:8001` (có thể đổi tuỳ ý).
- **Backend** PHP phục vụ API ở `127.0.0.1:8000`.
- **JWT** dùng cho admin, người dùng thường lưu token/đối tượng trong localStorage.

---

## 🛠️ Bộ công nghệ

| Lớp | Công nghệ |
|------|-----------|
| **Backend** | PHP 8.2+, PDO, JSON, JWT tự viết, Key4U API, Qwen API |
| **Frontend** | HTML5, CSS3 (glassmorphism), JavaScript ES6+, Fetch API, DOM API, localStorage |
| **Tools** | Composer, Git, PHP built-in server, PowerShell/Bash scripts, MySQL 8+, SQLite (tuỳ chọn) |

---

## ⚡ Hướng dẫn cài đặt & chạy

### 1. Chuẩn bị

- PHP ≥ 8.2 (bật `curl`, `json`, `pdo_mysql`).
- MySQL ≥ 8.0 (hoặc MariaDB 10.6+).
- Composer, Git.
- Windows 10/11 hoặc Linux/macOS.

### 2. Thiết lập tự động (khuyến nghị)

```powershell
# Tạo database, sinh config.env, import schema
.\setup-database.ps1

# Khởi động backend (8000) + frontend (8001)
.\start.bat
```

> Linux/macOS: `chmod +x setup-database.sh && ./setup-database.sh`.

### 3. Thiết lập thủ công

```bash
# Tạo database
mysql -u root -p -e "CREATE DATABASE thuvien_ai CHARACTER SET utf8mb4";
mysql -u root -p thuvien_ai < src/php-backend/tools/mysql-schema.sql

# Cấu hình
cp config.env.example config.env  # Windows dùng copy
# sửa DB_HOST, DB_USERNAME, DB_PASSWORD...

# Chạy dev server
cd src/php-backend && php -S 127.0.0.1:8000 server.php
cd ../web         && php -S 127.0.0.1:8001
```

### 4. Tích hợp API Key (tuỳ chọn)

```env
# config.env
KEY4U_API_KEY=sk-your-key4u...
```

- Không có Key4U API → hệ thống dùng Qwen miễn phí.
- Có Key4U API → mở khoá GPT-4, Claude, Gemini, Midjourney, Suno…

---

## 🔌 API & Tích hợp AI

| Endpoint | Method | Mô tả | Ghi chú |
|----------|--------|-------|--------|
| `/api/auth.php` | POST | Đăng ký/đăng nhập | body `{"action": "register" | "login"}` |
| `/api/admin.php` | GET/POST | Quản trị users, models, credits | yêu cầu Bearer token admin |
| `/api/chat-real.php` | POST | Gửi tin nhắn tới Key4U/Qwen | body `message`, `model`, `mode` |
| `/api/documents.php` | POST | Upload & xử lý tài liệu | trả về link/ngữ cảnh |
| `/api/models.php` | GET | Đồng bộ danh sách mô hình | gộp từ Key4U, Qwen, file cấu hình |
| `/api/health.php` | GET | Health check | response `{ status: "ok" }` |

Ví dụ: gửi tin nhắn tới GPT-4o
```json
POST /api/chat-real.php
{
  "message": "Tóm tắt tài liệu này",
  "model": "gpt-4o",
  "mode": "single"
}
```

---

## 🖥️ Giao diện & Trải nghiệm

### Trang chat (index.html)
- Sidebar chọn model theo provider, bộ lọc nâng cao.
- Ô tìm kiếm gợi ý từ khoá phổ biến.
- Vùng chat realtime với hiệu ứng typing, lưu lịch sử.
- Hiển thị credits, tên hiển thị, liên kết dashboard/ngắt kết nối.

### Admin Dashboard
- Header hiển thị avatar, tên admin, nút logout.
- Widget thống kê: tổng users, active users, tổng credits, số model.
- Bảng người dùng: ID, username, email, role, trạng thái, credits, hành động.
- Modal chỉnh credits: cộng/trừ/đặt giá trị ngay lập tức.
- Lưới mô hình AI (cập nhật tự động từ Key4U/Qwen).

### Trang phụ
- Login/Register: hiệu ứng floating label, validation realtime.
- Document manager: upload nhiều định dạng, xem tiến trình.
- Pricing: bảng giá credits (mockup), CTA rõ ràng.

---

## 🔒 Bảo mật & Hiệu năng

- **AuthMiddleware** dùng lại hàm `getTokenFromRequest()` → tương thích mọi server.
- **JWT** cho admin, token 24h, kiểm tra role trước khi trả dữ liệu.
- **Client**: loại bỏ dữ liệu `undefined` trong localStorage, dọn token khi 401, đồng bộ đa tab.
- **API**: validate đầu vào, hạn chế lộ thông báo lỗi nội bộ, trả JSON thống nhất.
- **Hiệu năng**: cache model list ở frontend, lazy-load lịch sử, tối giản dependency.

---

## 🧭 Roadmap

| Trạng thái | Hạng mục |
|------------|----------|
| ✅ Hoàn tất | Chat đa model, ENSEMBLE, dashboard admin, sửa lỗi header authorization |
| 🔄 Đang làm | Đồng bộ credits realtime giữa tab, tối ưu bộ lọc model (phân trang, fuzzy search) |
| 📌 Kế hoạch | Đa ngôn ngữ, plugin AI (tích hợp các dịch vụ nội bộ), analytics nâng cao, mobile app |

---

## 🤝 Đóng góp & Hỗ trợ

- **Pull Request**: fork, tạo branch, viết mô tả ngắn gọn, đảm bảo chạy `setup-database` & `start` OK.
- **Issue**: mô tả rõ môi trường (OS, PHP version, log kèm theo).
- **Tài liệu**: `README.md`, `AI_MODELS_LIST.md`, `DATABASE_SETUP.md`.
- **Email hỗ trợ**: `support@thuvienai.com` (trong phạm vi demo/POC).

---

## 📄 Thông tin dự án

- **Giấy phép**: MIT License → xem [LICENSE](LICENSE).
- **Nhóm phát triển**:
  - Trần Hải Bằng – Nhóm trưởng
  - Lê Huy Hoàng – 077205003839
  - Lương Thị Bích Hằng – Thành viên
  - Phan Minh Hòa – Thành viên
  - Hồ Ngọc Quyền – Thành viên

---

**© 2025 Thư Viện AI**  
*Được xây dựng với ❤️ bằng PHP, JavaScript và những công nghệ web hiện đại.*