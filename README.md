# 🧠 Thư Viện AI – Nền tảng chat đa mô hình

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb4.svg)](https://www.php.net/)
[![Python](https://img.shields.io/badge/Python-3.10%2B-3776ab.svg)](https://www.python.org/)
[![FastAPI](https://img.shields.io/badge/FastAPI-ready-009485.svg)](https://fastapi.tiangolo.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

“Thư Viện AI” là sandbox phục vụ nghiên cứu và triển khai thực tế cho hệ thống chat đa mô hình. Dự án kết hợp **PHP backend**, **FastAPI microservice** và **frontend thuần HTML/CSS/JS**, hỗ trợ xử lý tài liệu, sinh file theo yêu cầu và quản trị người dùng, đồng thời cho phép tích hợp nhiều mô hình từ Key4U và OpenAI.

---

## 📋 Mục lục

1. [Tổng quan kiến trúc](#-tổng-quan-kiến-trúc)
2. [Tính năng nổi bật](#-tính-năng-nổi-bật)
3. [Yêu cầu môi trường](#-yêu-cầu-môi-trường)
4. [Hướng dẫn cài đặt nhanh](#-hướng-dẫn-cài-đặt-nhanh)
5. [Chi tiết cấu hình](#-chi-tiết-cấu-hình)
6. [Luồng xử lý tài liệu](#-luồng-xử-lý-tài-liệu)
7. [Danh sách API PHP](#-danh-sách-api-php)
8. [Hướng dẫn frontend](#-hướng-dẫn-frontend)
9. [Khắc phục sự cố thường gặp](#-khắc-phục-sự-cố-thường-gặp)
10. [Đóng góp và phát triển](#-đóng-góp-và-phát-triển)
11. [Thông tin nhóm & giấy phép](#-thông-tin-nhóm--giấy-phép)

---

## 🏗 Tổng quan kiến trúc

```
chatbots-web/
├── config.env                  # cấu hình chung cho PHP backend
├── start.bat                   # script khởi động toàn hệ thống (Windows)
├── src/
│   ├── php-backend/            # Backend PHP thuần (router.php, API, services...)
│   │   ├── api/                # auth.php, chat-real.php, ai-tool.php...
│   │   ├── middleware/         # AuthMiddleware (JWT)
│   │   ├── services/           # Key4UService, AIToolService...
│   │   └── tools/AI tool/      # FastAPI microservice (Python)
│   └── web/                    # Frontend tĩnh (HTML/CSS/JS)
└── data/                       # dữ liệu mẫu, uploads, schema SQL
```

- Frontend: `http://127.0.0.1:8002`
- PHP API (router): `http://127.0.0.1:8000`
- FastAPI AI Tool: `http://127.0.0.1:8001`

Mọi request từ frontend đi qua PHP backend nhằm tái sử dụng hệ thống auth, quota, logging trước khi chuyển tới dịch vụ AI.

---

## ✨ Tính năng nổi bật

### Người dùng cuối
- Chat realtime với hơn **450 mô hình** (GPT-4, Claude, Gemini, Qwen, DeepSeek...).
- Upload tài liệu (PDF, DOCX, XLSX, TXT…) rồi yêu cầu AI tóm tắt hoặc tạo file mới.
- Lệnh “tạo file <định dạng>” giúp sinh mã nguồn/document; kết quả hiển thị trong chat và cung cấp **link tải thủ công**.
- Lưu lịch sử hội thoại ở localStorage, khôi phục lại sau khi tải trang.

### Quản trị viên
- Dashboard thống kê credits, người dùng, nhật ký hoạt động.
- Thao tác khóa/mở tài khoản, cấp thêm credits, đồng bộ danh sách mô hình.
- Cấu hình linh hoạt môi trường, key AI, timeout cho microservice.

### FastAPI AI Tool
- Nhận file, trích xuất nội dung bằng PyPDF2, python-docx, pandas…
- Đồng bộ hóa API Key giữa PHP và Python (qua header `Authorization` hoặc `X-Internal-Key`).
- Giao tiếp với Key4U API (hoặc OpenAI) để lấy kết quả, sau đó trả về text/JSON/file.

---

## 🧰 Yêu cầu môi trường

| Thành phần | Phiên bản khuyến nghị | Ghi chú |
|------------|-----------------------|--------|
| PHP        | 8.2 trở lên           | Bật `curl`, `pdo_mysql`, `json`, `fileinfo` |
| Python     | 3.10 trở lên          | Cần `venv`, `pip` |
| MySQL      | 8.0+ hoặc MariaDB 10.6+ | Import schema từ thư mục `data/database` |
| Node.js    | 18+ *(tuỳ chọn)*      | Chạy static server khác nếu muốn |
| OS         | Windows 10/11, macOS, Linux | Script `start.bat` tối ưu cho Windows |

---

## ⚙ Hướng dẫn cài đặt nhanh

### 1. Clone và chuẩn bị mã nguồn
```bash
git clone https://github.com/your-org/chatbots-web.git
cd chatbots-web
```

### 2. Tạo file cấu hình và database
```bash
cp config.env.example config.env               # Windows: copy config.env.example config.env

# Chỉnh sửa config.env theo môi trường: DB_HOST, KEY4U_API_KEY...

mysql -u root -p -e "CREATE DATABASE thuvien_ai CHARACTER SET utf8mb4"
mysql -u root -p thuvien_ai < data/database/mysql-schema.sql
```

### 3. Cài đặt FastAPI microservice
```bash
cd src/php-backend/tools/AI\ tool
python -m venv .venv
# Windows PowerShell
.venv\Scripts\Activate.ps1
# macOS / Linux
source .venv/bin/activate

pip install -r requirements.txt
```

### 4. Khởi chạy toàn bộ hệ thống (Windows)
```powershell
cd C:\path\to\chatbots-web
.\start.bat
```
`start.bat` sẽ:
1. Kiểm tra PHP & Python.
2. Kill tiến trình cũ (php.exe, python.exe, uvicorn.exe).
3. Mở 3 cửa sổ: PHP backend (`127.0.0.1:8000`), FastAPI (`127.0.0.1:8001`), frontend (`127.0.0.1:8002`).
4. Tự động mở trình duyệt tới trang chủ.

### 5. Khởi chạy thủ công (Linux/macOS hoặc môi trường tùy chỉnh)
```bash
# Terminal 1 - PHP backend
cd src/php-backend
php -S 127.0.0.1:8000 router.php

# Terminal 2 - FastAPI
cd src/php-backend/tools/AI\ tool
uvicorn main:app --host 127.0.0.1 --port 8001 --reload

# Terminal 3 - Frontend tĩnh
cd src/web
php -S 127.0.0.1:8002
```

Truy cập `http://127.0.0.1:8002` để trải nghiệm.

---

## 🔧 Chi tiết cấu hình

### 1. PHP backend – `config.env`
```env
KEY4U_API_KEY=sk-key4u-your-key
AI_TOOL_BASE_URL=http://127.0.0.1:8001
AI_TOOL_TIMEOUT=120
# AI_TOOL_INTERNAL_KEY=optional-shared-secret

DB_HOST=localhost
DB_NAME=thuvien_ai
DB_USERNAME=root
DB_PASSWORD=your-password

JWT_SECRET=thuvien-ai-super-secret-jwt-key
SERVER_PORT=8000
```

### 2. FastAPI AI Tool – `src/php-backend/tools/AI tool/.env`
```env
# Ưu tiên dùng chung KEY4U_API_KEY
KEY4U_API_KEY=sk-key4u-your-key
KEY4U_API_URL=https://api.key4u.shop/v1/chat/completions

# Tuỳ chọn nếu gọi trực tiếp OpenAI
# AI_API_KEY=sk-openai-your-key
AI_MODEL=gpt-4o
```

### 3. Thay đổi hạn mức upload
`start.bat` đã tăng `upload_max_filesize`, `post_max_size` lên 64MB và `memory_limit` 256MB. Nếu tự chạy, hãy thêm tham số khi khởi động PHP server:
```bash
php -d upload_max_filesize=64M -d post_max_size=64M -d memory_limit=256M -S 127.0.0.1:8000 router.php
```

---

## 📄 Luồng xử lý tài liệu

1. Người dùng nhấn **Tải nhanh** và chọn file. Frontend lưu trạng thái, chưa gửi lên server.
2. Khi nhấn **Gửi** kèm prompt (ví dụ “tạo file python tính toán cơ bản”), frontend gửi `FormData` tới `POST /api/ai-tool` gồm:
   - `file`
   - `user_prompt`
   - `output_format` (auto hoặc định dạng suy ra từ prompt)
   - `Authorization` header / trường dự phòng `auth_token`
3. PHP proxy (`api/ai-tool.php`) xác thực JWT, gọi `AIToolService`.
4. `AIToolService` gửi yêu cầu tới FastAPI bằng `multipart/form-data`, đính kèm header `X-Internal-Key` nếu có.
5. FastAPI đọc file tạm, trích nội dung, gọi Key4U API.
6. Kết quả trả về:
   - `text/json`: PHP trả lại JSON `{ success: true, data: ... }`.
   - `file` (ví dụ docx): PHP gửi file nhị phân về frontend.
7. Frontend hiển thị kết quả trong chat. Nếu là file, tạo **link tải thủ công** bằng `createDownloadLink(); URL tự revoke sau 5 phút`.

---

## 🔌 Danh sách API PHP

| Endpoint | Mô tả | Yêu cầu |
|----------|-------|---------|
| `POST /api/auth.php?action=login` | Đăng nhập, trả JWT + thông tin user | Body JSON `username`, `password` |
| `POST /api/chat-real.php` | Chat trực tiếp (không upload file) | Header `Authorization: Bearer <JWT>` |
| `POST /api/ai-tool` | Proxy xử lý tài liệu qua FastAPI | `multipart/form-data`, cần token |
| `POST /api/documents.php?action=upload` | Upload tài liệu lưu trên hệ thống | Giới hạn 10MB |
| `GET /api/models.php` | Danh sách mô hình hiển thị ở UI | Không bắt buộc auth |
| `GET /api/health` | Kiểm tra tình trạng backend | Trả về JSON `status` |

Lưu ý: AuthMiddleware sẽ tìm JWT theo thứ tự `Authorization` header → `auth_token` (POST body) → các biến session.

---

## 💡 Hướng dẫn frontend

- File chính: `src/web/script-backend.js`.
- Các helper quan trọng:
  - `processUploadedDocument`: gửi file lên PHP backend, trả kết quả thô.
  - `sendMessage`: phân tích prompt, hiển thị tin nhắn và tạo link tải thủ công.
  - `createDownloadLink`: tạo `Blob URL`, tự revoke sau 5 phút.
  - `formatMessageContent`: hiển thị markdown, code block.
- Lịch sử hội thoại lưu ở `localStorage` (key `chat_conversations`).
- Để tránh lỗi `localStorage undefined`, hàm `getRawUserData` và `parseUserDataSafe` đã xử lý các giá trị `null`, `'undefined'`.

---

## 🛠 Khắc phục sự cố thường gặp

| Vấn đề | Nguyên nhân | Cách xử lý |
|--------|-------------|------------|
| 401 Unauthorized khi call `/api/ai-tool` | Token hết hạn hoặc header thiếu | Đăng nhập lại, đảm bảo header `Authorization` tồn tại |
| `POST Content-Length exceeds limit` | PHP giới hạn upload | Chạy PHP với tham số `-d upload_max_filesize=64M -d post_max_size=64M` |
| `Call to undefined function mime_content_type()` | Chưa bật extension `fileinfo` | Cài / bật `php_fileinfo.dll` hoặc để hệ thống fallback theo đuôi file |
| FastAPI trả lỗi `Incorrect API key` | Key chưa đồng bộ giữa PHP và Python | Kiểm tra `KEY4U_API_KEY`, `AI_TOOL_INTERNAL_KEY`, biến môi trường | 
| Frontend hiển thị `%PDF-1.3` | Trước đây auto download file | Hiện tại đã chuyển sang link tải thủ công, refresh lại UI |

---

## 🤝 Đóng góp và phát triển

1. Fork repository, tạo branch mới mô tả rõ chức năng (`feature/file-upload`, `fix/login`...).
2. Chạy `start.bat` (hoặc các lệnh thủ công) đảm bảo môi trường hoạt động.
3. Commit nhỏ, rõ ràng; sử dụng tiếng Việt hoặc tiếng Anh nhất quán.
4. Khi gửi PR, đính kèm log/ảnh chụp màn hình nếu liên quan tới UI hoặc lỗi.
5. Góp ý, báo lỗi qua Issues: vui lòng ghi rõ bước tái hiện, trích log từ PHP FastAPI và console.

---

## 👥 Thông tin nhóm & giấy phép

- Trần Hải Bằng – 080205005769 (nhóm trưởng)
- Lê Huy Hoàng – 077205003839 (thư ký)
- Lương Thị Bích Hằng – Thành viên
- Phan Minh Hòa – Thành viên
- Hồ Ngọc Quyền – Thành viên

Giấy phép: [MIT](LICENSE) – tự do sử dụng, chỉnh sửa, phân phối theo điều khoản MIT.

---

**© 2025 Thư Viện AI** – xây dựng với ❤️ bằng PHP, FastAPI và JavaScript.