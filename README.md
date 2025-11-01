# 🧠 Thư Viện AI – Nền tảng chat đa mô hình

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://www.php.net/)
[![Python](https://img.shields.io/badge/Python-3.10%2B-blue.svg)](https://www.python.org/)
[![FastAPI](https://img.shields.io/badge/FastAPI-ready-009485.svg)](https://fastapi.tiangolo.com/)
[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

“Thư Viện AI” là một sandbox kết hợp **PHP backend**, **FastAPI AI Tool microservice** và **frontend thuần HTML/CSS/JS**. Mục tiêu: mang lại trải nghiệm chat đa mô hình, xử lý tài liệu, tạo file theo yêu cầu và quản trị người dùng với UI thân thiện.

---

## 📚 Nội dung chính

1. [Kiến trúc](#kiến-trúc)
2. [Tính năng nổi bật](#tính-năng-nổi-bật)
3. [Chuẩn bị môi trường](#chuẩn-bị-môi-trường)
4. [Cài đặt & chạy nhanh](#cài-đặt--chạy-nhanh)
5. [Cấu hình quan trọng](#cấu-hình-quan-trọng)
6. [Luồng xử lý tài liệu](#luồng-xử-lý-tài-liệu)
7. [API PHP chính](#api-php-chính)
8. [Front-end tips](#front-end-tips)
9. [Đóng góp](#đóng-góp)
10. [Thông tin nhóm](#thông-tin-nhóm)

---

## 🏗️ Kiến trúc

```
chatbots-web/
├── config.env                 # cấu hình chung
├── start.bat                  # script khởi động (Windows)
├── src/
│   ├── php-backend/          # Backend PHP thuần (routing thủ công)
│   │   ├── api/              # auth.php, chat-real.php, documents.php, ai-tool.php...
│   │   ├── services/         # Key4UService, AIService, DocumentService...
│   │   ├── tools/AI tool/    # FastAPI worker (Python)
│   │   └── middleware/       # JWT AuthMiddleware
│   └── web/                  # Frontend tĩnh (index.html, admin, login, script-backend.js...)
└── data/                     # uploads, sqlite (tùy chọn)
```

- **Frontend:** `127.0.0.1:8002` (chạy bằng PHP server hoặc bất kỳ static server).
- **PHP API:** `127.0.0.1:8000` (các endpoint REST).
- **FastAPI AI Tool:** `127.0.0.1:8001` (xử lý tài liệu, gọi mô hình Key4U/OpenAI).

---

## ✨ Tính năng nổi bật

### Người dùng cuối
- Đăng ký / đăng nhập, lưu phiên localStorage an toàn.
- Chọn nhanh hơn **450+ model** (GPT-4, Claude, Gemini, Qwen, DeepSeek...)
- Chat realtime, hiển thị markdown/code block đẹp mắt.
- Upload tài liệu (PDF/DOCX/Excel/...) và ra lệnh “tạo file python/md/...”.
- Nhận link tải thủ công để chủ động tải file kết quả.

### Quản trị viên
- Dashboard credits, danh sách người dùng, ghi nhật ký truy cập.
- Tùy chỉnh credits, khóa/mở tài khoản, xem tổng hợp mô hình.

### AI Tool (FastAPI)
- Parse tài liệu (PyPDF2, python-docx, pandas...).
- Gửi prompt tới Key4U API (đa nhà cung cấp) hoặc OpenAI nếu có key.
- Sinh nội dung text/JSON/CSV... theo yêu cầu và trả về cho PHP backend.

---

## 🧰 Chuẩn bị môi trường

| Thành phần | Phiên bản khuyến nghị |
|------------|-----------------------|
| PHP        | 8.2+ (bật ext `curl`, `pdo_mysql`, `json`) |
| Python     | 3.10+ (pip, virtualenv) |
| Node (tùy chọn)| 18+ (nếu muốn chạy static server) |
| MySQL      | 8.0+ hoặc MariaDB 10.6+ |
| Hệ điều hành | Windows 10/11, macOS, Linux |

---

## ⚙️ Cài đặt & chạy nhanh

### 1. Clone project
```bash
git clone https://github.com/your-org/chatbots-web.git
cd chatbots-web
```

### 2. Tạo database & copy cấu hình
```bash
cp config.env.example config.env
# hoặc trên Windows: copy config.env.example config.env

# chỉnh config.env: DB_HOST, DB_USERNAME, KEY4U_API_KEY...
mysql -u root -p -e "CREATE DATABASE thuvien_ai CHARACTER SET utf8mb4"
mysql -u root -p thuvien_ai < data/database/mysql-schema.sql
```

### 3. Cài dependency cho FastAPI worker
```bash
cd src/php-backend/tools/AI\ tool
python -m venv .venv
source .venv/bin/activate  # Windows: .\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

### 4. Khởi chạy toàn hệ thống (Windows)
```powershell
.\start.bat
# script sẽ mở 3 cửa sổ: PHP backend (8000), FastAPI (8001), frontend (8002)
```

Linux/macOS: chạy thủ công từng dịch vụ:
```bash
# Terminal 1: PHP backend
cd src/php-backend
php -S 127.0.0.1:8000 router.php

# Terminal 2: FastAPI worker
cd src/php-backend/tools/AI\ tool
uvicorn main:app --host 127.0.0.1 --port 8001 --reload

# Terminal 3: Frontend server
cd src/web
php -S 127.0.0.1:8002
```

Truy cập `http://127.0.0.1:8002` để sử dụng.

---

## 🔧 Cấu hình quan trọng

`config.env`
```env
KEY4U_API_KEY=sk-key4u-your-key
SERVER_PORT=8000
DB_HOST=localhost
DB_NAME=thuvien_ai
DB_USERNAME=root
DB_PASSWORD=...
JWT_SECRET=thuvien-ai-super-secret-jwt-key
AI_TOOL_BASE_URL=http://127.0.0.1:8001
AI_TOOL_TIMEOUT=120
# AI_TOOL_INTERNAL_KEY=optional-shared-key
```

`src/php-backend/tools/AI tool/.env`
```env
KEY4U_API_KEY=sk-key4u-your-key
 KEY4U_API_URL=https://api.key4u.shop/v1/chat/completions
# Hoặc dùng AI_API_KEY nếu gọi trực tiếp OpenAI
AI_MODEL=gpt-4-turbo
```

---

## 📄 Luồng xử lý tài liệu

1. Người dùng chọn file qua nút **📎 Tải nhanh** (frontend chỉ lưu lại, không gửi ngay).
2. Khi nhấn **Gửi** kèm câu như “tạo file python…”, frontend gửi multipart tới `/api/ai-tool`:
   - file upload
   - `user_prompt`
   - `output_format` (auto hoặc do người dùng yêu cầu)
   - token đăng nhập
3. PHP proxy gọi FastAPI worker.
4. FastAPI đọc file, tạo prompt, gọi Key4U/OpenAI → nhận phản hồi text.
5. PHP trả kết quả về frontend.
6. Frontend hiển thị nội dung trong chat; nếu yêu cầu định dạng, tạo **link tải thủ công** để người dùng tự click.

---

## 🔌 API PHP chính

| Endpoint | Mô tả | Notes |
|----------|-------|-------|
| `POST /api/auth.php?action=login` | Đăng nhập, trả về JWT + thông tin user | lưu vào `localStorage` |
| `POST /api/chat-real.php` | Chat thường qua Key4U/Qwen | cần `user_token` trong header |
| `POST /api/ai-tool` | Proxy gửi file, prompt tới FastAPI | bắt buộc Bearer token |
| `POST /api/documents.php?action=upload` | Lưu tài liệu vào hệ thống | hỗ trợ 10MB |
| `GET /api/models.php` | Trả về danh sách mô hình đã đồng bộ | hiển thị ở sidebar |

- Token lưu ở key `user_token` (đã đồng bộ với frontend).
- AuthMiddleware đọc header `Authorization: Bearer <JWT>` hoặc trường `auth_token` trong form.

---

## 💡 Front-end tips

- `script-backend.js`: giữ toàn bộ logic chat, upload, định dạng tin nhắn.
- Markdown + code block hiển thị bằng hàm `formatMessageContent`.
- Link tải thủ công được tạo bằng `createDownloadLink`, tự revoke sau 5 phút.
- Lưu lịch sử chat trong `localStorage` (key `chat_conversations`).
- Nếu thấy console báo “userData undefined”, đăng nhập lại để token hợp lệ.

---

## 🤝 Đóng góp

1. Fork repo và tạo branch mới.
2. Chạy `start.bat` hoặc các lệnh thủ công đảm bảo hệ thống hoạt động.
3. Commit nhỏ gọn; PR mô tả rõ thay đổi.
4. Báo bug: cung cấp log PHP/FastAPI, request payload.

---

## 👥 Thông tin nhóm

- Trần Hải Bằng – Nhóm trưởng
- Lê Huy Hoàng – 077205003839
- Lương Thị Bích Hằng – Thành viên
- Phan Minh Hòa – Thành viên
- Hồ Ngọc Quyền – Thành viên

Giấy phép: [MIT](LICENSE)

---

**© 2025 Thư Viện AI** – xây dựng với ❤️ bằng PHP, FastAPI và JavaScript.