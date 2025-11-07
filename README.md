# 🧠 Thư Viện AI – Nền tảng chat đa mô hình

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb4.svg)](https://www.php.net/)
[![Python](https://img.shields.io/badge/Python-3.10%2B-3776ab.svg)](https://www.python.org/)
[![FastAPI](https://img.shields.io/badge/FastAPI-ready-009485.svg)](https://fastapi.tiangolo.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

"Thư Viện AI" là sandbox phục vụ nghiên cứu và triển khai thực tế cho hệ thống chat đa mô hình. Dự án kết hợp **PHP backend**, **FastAPI microservice** và **frontend thuần HTML/CSS/JS**, hỗ trợ xử lý tài liệu, sinh file theo yêu cầu và quản trị người dùng, đồng thời cho phép tích hợp nhiều mô hình từ Key4U và OpenAI.

---

## 📋 Mục lục

1. [Tổng quan kiến trúc](#-tổng-quan-kiến-trúc)
2. [Tính năng nổi bật](#-tính-năng-nổi-bật)
3. [Yêu cầu hệ thống](#-yêu-cầu-hệ-thống)
4. [Hướng dẫn cài đặt với XAMPP](#-hướng-dẫn-cài-đặt-với-xampp)
5. [Hướng dẫn cài đặt thủ công (không dùng XAMPP)](#-hướng-dẫn-cài-đặt-thủ-công)
6. [Chi tiết cấu hình](#-chi-tiết-cấu-hình)
7. [Khởi động hệ thống](#-khởi-động-hệ-thống)
8. [Luồng xử lý tài liệu](#-luồng-xử-lý-tài-liệu)
9. [Danh sách API](#-danh-sách-api)
10. [Khắc phục sự cố](#-khắc-phục-sự-cố)
11. [Đóng góp và phát triển](#-đóng-góp-và-phát-triển)

---

## 🏗 Tổng quan kiến trúc

```
chatbots-web/
├── config.env                  # Cấu hình chung cho PHP backend
├── start.bat                   # Script khởi động hệ thống (Windows)
├── src/
│   ├── php-backend/            # Backend PHP thuần
│   │   ├── api/                # auth.php, chat-real.php, ai-tool.php...
│   │   ├── middleware/         # AuthMiddleware (JWT)
│   │   ├── services/           # Key4UService, AIToolService...
│   │   └── tools/AI tool/      # FastAPI microservice (Python)
│   └── web/                    # Frontend tĩnh (HTML/CSS/JS)
└── data/                       # Database schema, uploads
```

**Kiến trúc mạng:**
- **Frontend**: `http://127.0.0.1:8002` - Giao diện người dùng
- **PHP API**: `http://127.0.0.1:8000` - Backend xử lý requests
- **FastAPI AI Tool**: `http://127.0.0.1:8001` - Microservice xử lý AI

Mọi request từ frontend đi qua PHP backend để xác thực, quản lý quota và logging trước khi chuyển tới dịch vụ AI.

---

## ✨ Tính năng nổi bật

### Người dùng cuối
- 💬 Chat realtime với hơn **450 mô hình AI** (GPT-4, Claude, Gemini, Qwen, DeepSeek...)
- 📄 Upload và xử lý tài liệu (PDF, DOCX, XLSX, TXT...)
- 📝 Tạo file tự động theo yêu cầu (Python, JavaScript, Markdown...)
- 💰 Hệ thống credits - mỗi câu hỏi trừ 1 credit
- 💾 Lưu lịch sử hội thoại trong localStorage

### Quản trị viên
- 📊 Dashboard thống kê credits, người dùng, nhật ký
- 👥 Quản lý người dùng: khóa/mở tài khoản, cấp credits
- 🔧 Cấu hình linh hoạt môi trường, API keys

### FastAPI AI Tool
- 🔄 Nhận file, trích xuất nội dung tự động
- 🔐 Đồng bộ hóa API Key giữa PHP và Python
- 🌐 Giao tiếp với Key4U API/OpenAI

---

## 🧰 Yêu cầu hệ thống

| Thành phần | Phiên bản khuyến nghị | Ghi chú |
|------------|-----------------------|---------|
| **PHP** | 8.2+ | Bật extensions: `curl`, `pdo_mysql`, `json`, `fileinfo` |
| **Python** | 3.10+ | Cần `venv`, `pip` |
| **MySQL** | 8.0+ hoặc MariaDB 10.6+ | Hoặc dùng MySQL trong XAMPP |
| **Node.js** | 18+ *(tuỳ chọn)* | Chỉ cần nếu không dùng PHP built-in server |
| **OS** | Windows 10/11, macOS, Linux | Khuyến nghị Windows với XAMPP |

**Khuyến nghị:** Sử dụng **XAMPP** để dễ dàng cài đặt PHP, MySQL và Apache cùng lúc.

---

## 📦 Hướng dẫn cài đặt với XAMPP

### Bước 1: Tải và cài đặt XAMPP

1. **Tải XAMPP:**
   - Truy cập: https://www.apachefriends.org/download.html
   - Chọn phiên bản phù hợp với hệ điều hành (Windows khuyến nghị)
   - Download file `.exe` (khoảng 150MB)

2. **Cài đặt XAMPP:**
   - Chạy file installer với quyền Administrator
   - Chọn thư mục cài đặt (mặc định: `C:\xampp`)
   - Chọn các thành phần cần thiết:
     - ✅ **Apache** (bắt buộc)
     - ✅ **MySQL** (bắt buộc)
     - ✅ **PHP** (bắt buộc)
     - ✅ **phpMyAdmin** (khuyến nghị để quản lý database)
     - ⬜ Perl, FileZilla, Tomcat (không cần)
   - Nhấn **Next** và chờ cài đặt hoàn tất

3. **Khởi động XAMPP Control Panel:**
   - Mở **XAMPP Control Panel** từ Start Menu
   - Hoặc chạy file: `C:\xampp\xampp-control.exe`

### Bước 2: Khởi động Apache và MySQL

1. **Trong XAMPP Control Panel:**
   - Nhấn **Start** cho **Apache**
   - Nhấn **Start** cho **MySQL**
   - Kiểm tra cả 2 service đã chuyển sang màu xanh ✅

2. **Kiểm tra cài đặt:**
   - Mở trình duyệt, truy cập: `http://localhost`
   - Bạn sẽ thấy trang chủ XAMPP
   - Truy cập: `http://localhost/phpmyadmin` để kiểm tra MySQL

### Bước 3: Cấu hình PHP trong XAMPP

1. **Tìm file `php.ini`:**
   - Vị trí: `C:\xampp\php\php.ini`
   - Hoặc mở XAMPP Control Panel → Apache → **Config** → **PHP (php.ini)**

2. **Bật các extension cần thiết:**
   - Mở `php.ini` bằng Notepad++ hoặc editor khác
   - Tìm và bỏ dấu `;` (uncomment) các dòng sau:
   ```ini
   extension=curl
   extension=pdo_mysql
   extension=mysqli
   extension=fileinfo
   extension=json
   extension=mbstring
   ```
   - Lưu file

3. **Cấu hình upload (tùy chọn):**
   - Tìm và chỉnh sửa:
   ```ini
   upload_max_filesize = 64M
   post_max_size = 64M
   memory_limit = 256M
   max_execution_time = 300
   ```
   - Lưu file

4. **Thêm PHP vào PATH (Windows):**
   - Mở **System Properties** → **Environment Variables**
   - Tìm biến `Path` trong **System variables**
   - Thêm: `C:\xampp\php`
   - Thêm: `C:\xampp\php\ext`
   - Nhấn **OK** và khởi động lại Command Prompt

5. **Kiểm tra PHP:**
   ```cmd
   php --version
   ```
   - Bạn sẽ thấy phiên bản PHP (ví dụ: PHP 8.2.x)

### Bước 4: Cài đặt Python (nếu chưa có)

1. **Tải Python:**
   - Truy cập: https://www.python.org/downloads/
   - Download Python 3.10+ cho Windows

2. **Cài đặt Python:**
   - ✅ **Quan trọng:** Đánh dấu **"Add Python to PATH"**
   - Chọn **"Install Now"**
   - Chờ cài đặt hoàn tất

3. **Kiểm tra Python:**
   ```cmd
   python --version
   pip --version
   ```

### Bước 5: Clone và cấu hình dự án

1. **Clone repository:**
   ```bash
   git clone https://github.com/your-org/chatbots-web.git
   cd chatbots-web
   ```

2. **Tạo file cấu hình:**
   ```bash
   # Windows
   copy config.env.example config.env
   
   # Linux/macOS
   cp config.env.example config.env
   ```

3. **Chỉnh sửa `config.env`:**
   - Mở file `config.env` bằng Notepad hoặc editor
   - Cập nhật thông tin database:
   ```env
   DB_HOST=localhost
   DB_NAME=thuvien_ai
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   - ⚠️ **Lưu ý:** XAMPP MySQL mặc định không có password cho user `root`
   - Nếu bạn đã đặt password, điền vào `DB_PASSWORD`

4. **Thêm API Key (nếu có):**
   ```env
   KEY4U_API_KEY=sk-key4u-your-key-here
   JWT_SECRET=your-super-secret-jwt-key-change-this
   ```

### Bước 6: Cài đặt PHP dependencies (Composer)

**Nếu bạn muốn cài PHP dependencies trước, có thể làm ở bước này:**

1. **Kiểm tra Composer đã cài chưa:**
   ```cmd
   composer --version
   ```
   
   **Nếu chưa có Composer:**
   - **Windows:** Tải `Composer-Setup.exe` từ https://getcomposer.org/download/
   - Chạy installer và làm theo hướng dẫn
   - Đảm bảo PHP đã có trong PATH

2. **Di chuyển đến thư mục PHP backend:**
   ```cmd
   cd src\php-backend
   ```

3. **Cài đặt PHP dependencies:**
   ```cmd
   composer install
   ```
   
   **Hoặc production mode:**
   ```cmd
   composer install --no-dev --optimize-autoloader
   ```

### Bước 7: Setup Database với phpMyAdmin

#### Cách 1: Dùng phpMyAdmin (Khuyến nghị)

1. **Truy cập phpMyAdmin:**
   - Mở trình duyệt: `http://localhost/phpmyadmin`
   - Đăng nhập với:
     - **Username:** `root`
     - **Password:** (để trống nếu chưa đặt)

2. **Tạo database mới:**
   - Click tab **"Databases"**
   - Nhập tên database: `thuvien_ai`
   - Chọn **Collation:** `utf8mb4_unicode_ci`
   - Click **"Create"**

3. **Import schema:**
   - Click vào database `thuvien_ai` ở sidebar bên trái
   - Click tab **"Import"**
   - Click **"Choose File"** và chọn file: `data/database/mysql-schema.sql`
   - Click **"Go"** ở cuối trang
   - Đợi import hoàn tất (sẽ thấy thông báo thành công)

#### Cách 2: Dùng MySQL Command Line

1. **Mở MySQL Command Line:**
   - Mở Command Prompt
   - Chuyển đến thư mục XAMPP:
   ```cmd
   cd C:\xampp\mysql\bin
   ```

2. **Đăng nhập MySQL:**
   ```cmd
   mysql.exe -u root -p
   ```
   - Nhấn Enter nếu không có password
   - Hoặc nhập password nếu đã đặt

3. **Tạo database và import:**
   ```sql
   CREATE DATABASE thuvien_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE thuvien_ai;
   SOURCE C:/path/to/chatbots-web/data/database/mysql-schema.sql;
   EXIT;
   ```

#### Cách 3: Dùng PHP script

1. **Chạy script tự động:**
   ```cmd
   cd C:\path\to\chatbots-web
   php src/php-backend/tools/init-mysql.php
   ```
   - Script sẽ tự động tạo database và import schema

### Bước 8: Cài đặt thư viện requirements

#### 8.1. Cài đặt Python requirements (FastAPI microservice)

1. **Di chuyển đến thư mục AI Tool:**
   ```cmd
   cd src\php-backend\tools\AI tool
   ```

2. **Tạo virtual environment:**
   ```cmd
   python -m venv .venv
   ```
   - Virtual environment sẽ tạo thư mục `.venv` trong thư mục hiện tại

3. **Kích hoạt virtual environment:**
   ```cmd
   # Windows Command Prompt
   .venv\Scripts\activate.bat
   
   # Windows PowerShell (nếu bị chặn chính sách)
   .venv\Scripts\Activate.ps1
   # Nếu lỗi "cannot be loaded", chạy:
   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
   
   # Linux/macOS
   source .venv/bin/activate
   ```
   - Sau khi kích hoạt, bạn sẽ thấy `(.venv)` ở đầu dòng command prompt

4. **Nâng cấp pip (khuyến nghị):**
   ```cmd
   python -m pip install --upgrade pip
   ```

5. **Cài đặt Python dependencies từ requirements.txt:**
   ```cmd
   # Từ thư mục gốc dự án
   cd C:\path\to\chatbots-web
   pip install -r requirements.txt
   ```
   
   **Danh sách thư viện sẽ được cài:**
   - `fastapi` - Web framework cho FastAPI
   - `uvicorn` - ASGI server chạy FastAPI
   - `python-dotenv` - Đọc file .env
   - `openai` - Client cho OpenAI API
   - `PyPDF2` - Đọc file PDF
   - `python-docx` - Đọc file Word (DOCX)
   - `pandas` - Xử lý dữ liệu (Excel, CSV)
   - `fpdf2` - Tạo file PDF
   - `python-multipart` - Xử lý form data upload
   - 'pytesseract'
   - 'pdf2image' 
   - 'Pillow'

   **Nếu gặp lỗi khi cài đặt:**
   ```cmd
   # Thử cài từng package:
   pip install fastapi uvicorn
   pip install python-dotenv openai
   pip install PyPDF2 python-docx pandas fpdf2 python-multipart
   ```

6. **Kiểm tra cài đặt:**
   ```cmd
   python -c "import fastapi; import uvicorn; import openai; print('✅ All packages installed successfully!')"
   ```

#### 8.2. Cài đặt PHP dependencies (Composer)

1. **Kiểm tra Composer đã cài chưa:**
   ```cmd
   composer --version
   ```
   
   **Nếu chưa có Composer:**
   - **Windows:** Tải `Composer-Setup.exe` từ https://getcomposer.org/download/
   - Chạy installer và làm theo hướng dẫn
   - Đảm bảo PHP đã có trong PATH

2. **Di chuyển đến thư mục PHP backend:**
   ```cmd
   cd src\php-backend
   ```

3. **Cài đặt PHP dependencies từ composer.json:**
   ```cmd
   composer install
   ```
   
   **Hoặc nếu muốn cài production (không có dev dependencies):**
   ```cmd
   composer install --no-dev --optimize-autoloader
   ```
   
   **Danh sách thư viện sẽ được cài:**
   - `guzzlehttp/guzzle` - HTTP client cho API requests
   - `firebase/php-jwt` - Xử lý JWT tokens

4. **Kiểm tra cài đặt:**
   ```cmd
   # Kiểm tra vendor folder đã được tạo
   dir vendor
   
   # Hoặc test import:
   php -r "require 'vendor/autoload.php'; echo '✅ Composer packages loaded!';"
   ```

**Lưu ý quan trọng:**
- ✅ **Python virtual environment:** Luôn kích hoạt `.venv` trước khi chạy FastAPI
- ✅ **PHP Composer:** Chỉ cần chạy `composer install` một lần, sau đó tự động load khi chạy PHP
- ⚠️ **Windows PowerShell:** Có thể cần thay đổi ExecutionPolicy để chạy script activation

---

## ⚙️ Hướng dẫn cài đặt thủ công (không dùng XAMPP)

Nếu bạn không muốn dùng XAMPP, có thể cài đặt từng thành phần riêng:

### 1. Cài đặt PHP độc lập

1. **Tải PHP:**
   - Truy cập: https://windows.php.net/download/
   - Download PHP 8.2+ Thread Safe (ZIP)
   - Giải nén vào: `C:\php`

2. **Cấu hình:**
   - Copy `php.ini-development` thành `php.ini`
   - Bật extensions như hướng dẫn ở trên
   - Thêm PHP vào PATH

### 2. Cài đặt MySQL độc lập

1. **Tải MySQL:**
   - Truy cập: https://dev.mysql.com/downloads/installer/
   - Download MySQL Installer
   - Chọn **"Developer Default"** hoặc **"Server only"**

2. **Cấu hình:**
   - Đặt root password (nhớ ghi lại!)
   - Cập nhật `config.env` với password đã đặt

---

## 🔧 Chi tiết cấu hình

### 1. File `config.env` (ở thư mục gốc)

```env
# API Keys
KEY4U_API_KEY=sk-key4u-your-key-here
AI_TOOL_BASE_URL=http://127.0.0.1:8001
AI_TOOL_TIMEOUT=120

# Database (XAMPP mặc định)
DB_HOST=localhost
DB_NAME=thuvien_ai
DB_USERNAME=root
DB_PASSWORD=

# JWT Secret (thay đổi trong production!)
JWT_SECRET=thuvien-ai-super-secret-jwt-key-change-this

# Server Ports
SERVER_PORT=8000
```

### 2. File `src/php-backend/config.env` (nếu có)

```env
# Tương tự như config.env ở thư mục gốc
# Nếu file này tồn tại, nó sẽ được ưu tiên
```

### 3. File `src/php-backend/tools/AI tool/.env` (tùy chọn)

```env
KEY4U_API_KEY=sk-key4u-your-key-here
KEY4U_API_URL=https://api.key4u.shop/v1/chat/completions
AI_MODEL=gpt-4o
```

---

## 🚀 Khởi động hệ thống

### Cách 1: Dùng script tự động (Windows)

```cmd
# Trong thư mục gốc dự án
start.bat
```

Script này sẽ:
1. ✅ Kiểm tra PHP và Python đã cài đặt
2. ✅ Dừng các tiến trình cũ (nếu có)
3. ✅ Khởi động 3 server:
   - PHP Backend: `http://127.0.0.1:8000`
   - FastAPI AI Tool: `http://127.0.0.1:8001`
   - Frontend: `http://127.0.0.1:8002`
4. ✅ Tự động mở trình duyệt

### Cách 2: Khởi động thủ công

**Terminal 1 - PHP Backend:**
```cmd
cd src\php-backend
php -d upload_max_filesize=64M -d post_max_size=64M -d memory_limit=256M -S 127.0.0.1:8000 router.php
```

**Terminal 2 - FastAPI:**
```cmd
cd src\php-backend\tools\AI tool
.venv\Scripts\activate  # Windows
# hoặc: source .venv/bin/activate  # Linux/macOS
uvicorn main:app --host 127.0.0.1 --port 8001 --reload
```

**Terminal 3 - Frontend:**
```cmd
cd src\web
php -S 127.0.0.1:8002
```

### Cách 3: Dùng XAMPP Apache (không khuyến nghị)

Nếu muốn dùng Apache từ XAMPP thay vì PHP built-in server:

1. **Cấu hình Virtual Host:**
   - Mở: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
   - Thêm:
   ```apache
   <VirtualHost *:80>
       ServerName chatbots.local
       DocumentRoot "C:/path/to/chatbots-web/src/web"
       <Directory "C:/path/to/chatbots-web/src/web">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   
   <VirtualHost *:8000>
       ServerName api.local
       DocumentRoot "C:/path/to/chatbots-web/src/php-backend"
       <Directory "C:/path/to/chatbots-web/src/php-backend">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

2. **Thêm vào hosts file:**
   - Mở: `C:\Windows\System32\drivers\etc\hosts` (với quyền Admin)
   - Thêm:
   ```
   127.0.0.1    chatbots.local
   127.0.0.1    api.local
   ```

3. **Khởi động lại Apache từ XAMPP Control Panel**

⚠️ **Lưu ý:** Cách này phức tạp hơn và có thể gây conflict với các port. Khuyến nghị dùng cách 1 hoặc 2.

---

## 📄 Luồng xử lý tài liệu

1. **Người dùng upload file** → Frontend lưu vào `File` object
2. **Gửi message kèm file** → Frontend gửi `FormData` đến `POST /api/ai-tool`
3. **PHP xác thực JWT** → Kiểm tra token, trừ credits
4. **PHP proxy request** → Gửi đến FastAPI với `multipart/form-data`
5. **FastAPI xử lý** → Trích xuất nội dung file, gọi Key4U API
6. **Trả kết quả** → JSON hoặc file (link download)

**Ví dụ workflow:**
```
User: "Tạo file python tính toán cơ bản"
     ↓
Frontend → PHP Backend (xác thực, trừ credit)
     ↓
PHP → FastAPI (xử lý)
     ↓
FastAPI → Key4U API
     ↓
Response → Python code
     ↓
Frontend hiển thị + link download
```

---

## 🔌 Danh sách API

### Authentication
| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/api/auth.php?action=register` | POST | Đăng ký tài khoản mới |
| `/api/auth.php?action=login` | POST | Đăng nhập, trả JWT token |
| `/api/auth.php?action=profile` | GET | Lấy thông tin user (cần token) |

### Chat & AI
| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/api/chat-real.php` | POST | Chat với AI models |
| `/api/ai-tool` | POST | Xử lý tài liệu qua FastAPI |
| `/api/models.php` | GET | Danh sách AI models |

### Documents
| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/api/documents.php?action=upload` | POST | Upload tài liệu |
| `/api/documents.php?action=list` | GET | Danh sách tài liệu |

### Admin
| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/api/admin.php` | GET/POST | Quản lý users, credits (cần admin role) |

### System
| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/api/health.php` | GET | Health check |

**Lưu ý:** Hầu hết API yêu cầu header `Authorization: Bearer <JWT_TOKEN>`

---

## 🛠 Khắc phục sự cố

### ❌ Lỗi "Access denied for user 'root'@'localhost'"

**Nguyên nhân:** Sai thông tin đăng nhập MySQL

**Cách xử lý:**
1. Kiểm tra XAMPP Control Panel → MySQL đã chạy chưa
2. Kiểm tra `config.env`:
   ```env
   DB_USERNAME=root
   DB_PASSWORD=  # Để trống nếu XAMPP mặc định
   ```
3. Thử đăng nhập phpMyAdmin với thông tin tương tự
4. Nếu vẫn lỗi, đặt lại password MySQL:
   ```sql
   ALTER USER 'root'@'localhost' IDENTIFIED BY '';
   FLUSH PRIVILEGES;
   ```

### ❌ Lỗi "Call to undefined function curl_init()"

**Nguyên nhân:** Extension `curl` chưa bật

**Cách xử lý:**
1. Mở `C:\xampp\php\php.ini`
2. Tìm dòng: `;extension=curl`
3. Bỏ dấu `;` thành: `extension=curl`
4. Lưu file và khởi động lại Apache

### ❌ Lỗi "PDOException: could not find driver"

**Nguyên nhân:** Extension `pdo_mysql` chưa bật

**Cách xử lý:**
1. Mở `C:\xampp\php\php.ini`
2. Tìm và uncomment:
   ```ini
   extension=pdo_mysql
   extension=mysqli
   ```
3. Lưu và khởi động lại

### ❌ Port 8000 hoặc 8001 đã được sử dụng

**Nguyên nhân:** Có ứng dụng khác đang dùng port

**Cách xử lý:**
1. Tìm process đang dùng port:
   ```cmd
   netstat -ano | findstr :8000
   ```
2. Kill process (thay `<PID>` bằng số tìm được):
   ```cmd
   taskkill /PID <PID> /F
   ```
3. Hoặc đổi port trong `start.bat` và `config.env`

### ❌ FastAPI không khởi động được

**Nguyên nhân:** Thiếu dependencies hoặc virtual environment

**Cách xử lý:**
1. Đảm bảo đã kích hoạt virtual environment:
   ```cmd
   cd src\php-backend\tools\AI tool
   .venv\Scripts\activate
   ```
2. Cài đặt lại dependencies:
   ```cmd
   pip install -r requirements.txt
   ```
3. Kiểm tra uvicorn đã cài:
   ```cmd
   pip install uvicorn fastapi
   ```

### ❌ "ModuleNotFoundError: No module named 'xxx'"

**Nguyên nhân:** Thiếu Python package

**Cách xử lý:**
1. Đảm bảo đang trong virtual environment
2. Cài package còn thiếu:
   ```cmd
   pip install <package-name>
   ```

### ❌ Frontend không load được

**Cách xử lý:**
1. Kiểm tra frontend server đã chạy:
   ```cmd
   # Phải thấy: http://127.0.0.1:8002
   ```
2. Kiểm tra Console trong trình duyệt (F12) xem có lỗi CORS không
3. Kiểm tra network tab xem requests có bị block không

### ❌ Credit không bị trừ sau khi chat

**Cách xử lý:**
1. Kiểm tra token JWT có được gửi kèm request (Network tab → Headers)
2. Kiểm tra backend logs xem có log về credit deduction
3. Kiểm tra database xem credit có thay đổi:
   ```sql
   SELECT id, username, credits FROM users;
   ```

---

## 🤝 Đóng góp và phát triển

### Quy trình đóng góp

1. **Fork repository** và tạo branch mới:
   ```bash
   git checkout -b feature/ten-tinh-nang
   ```

2. **Commit changes:**
   ```bash
   git commit -m "Thêm tính năng XYZ"
   ```

3. **Push và tạo Pull Request:**
   ```bash
   git push origin feature/ten-tinh-nang
   ```

### Hướng dẫn code

- Sử dụng **tiếng Việt** cho comments và log messages
- Format code theo chuẩn PSR-12 (PHP) và PEP 8 (Python)
- Viết commit message rõ ràng, mô tả đầy đủ thay đổi
- Test kỹ trước khi commit

### Báo lỗi

Khi báo lỗi, vui lòng cung cấp:
- **OS và phiên bản:** Windows 10, macOS 13, etc.
- **PHP version:** `php --version`
- **Python version:** `python --version`
- **MySQL version:** Xem trong phpMyAdmin
- **Logs:** Console logs, server logs, error messages
- **Steps to reproduce:** Các bước tái hiện lỗi

---

## 📞 Liên hệ và hỗ trợ

- **GitHub Issues:** https://github.com/your-org/chatbots-web/issues
- **Email:** support@thuvienai.example (tùy chọn)

---

## 👥 Thông tin nhóm

- **Trần Hải Bằng** – 080205005769 (Nhóm trưởng)
- **Lê Huy Hoàng** – 077205003839 (Thư ký)
- **Lương Thị Bích Hằng** – Thành viên
- **Phan Minh Hòa** – Thành viên
- **Hồ Ngọc Quyền** – Thành viên

---

## 📄 Giấy phép

Dự án được phát hành dưới giấy phép **[MIT License](LICENSE)**.

---

## 🎯 Tóm tắt nhanh

### Cài đặt với XAMPP (10-15 phút)

```cmd
# 1. Cài XAMPP và khởi động Apache + MySQL
# 2. Cài Python 3.10+ và Composer
# 3. Clone repo và cấu hình config.env
# 4. Tạo database qua phpMyAdmin
# 5. Cài đặt Python requirements:
cd C:\path\to\chatbots-web
python -m venv src\php-backend\tools\AI tool\.venv
src\php-backend\tools\AI tool\.venv\Scripts\activate
pip install -r requirements.txt
# 6. Cài đặt PHP dependencies:
cd src\php-backend
composer install
# 7. Chạy start.bat
```

### Cài đặt requirements nhanh

**Python requirements (từ thư mục gốc):**
```cmd
cd C:\path\to\chatbots-web
pip install -r requirements.txt
```

**PHP dependencies (từ thư mục backend):**
```cmd
cd src\php-backend
composer install
```

### Truy cập

- **Frontend:** http://127.0.0.1:8002
- **Backend API:** http://127.0.0.1:8000/api/health
- **FastAPI Docs:** http://127.0.0.1:8001/docs
- **phpMyAdmin:** http://localhost/phpmyadmin

---

**© 2025 Thư Viện AI** – Xây dựng với ❤️ bằng PHP, FastAPI và JavaScript.
