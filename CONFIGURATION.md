# 🔧 Hướng dẫn cấu hình Thư Viện AI

## 📋 Tổng quan

Dự án Thư Viện AI hỗ trợ 2 loại database:
- **SQLite** (mặc định, dễ setup)
- **MySQL** (production, performance tốt hơn)

## 🗄️ Cấu hình Database

### Option 1: SQLite (Đơn giản)

```env
# Cấu hình database
DATABASE_PATH=data/database/xuandat_ai.db
```

**Ưu điểm:**
- ✅ Không cần cài đặt database server
- ✅ File database tự động tạo
- ✅ Phù hợp cho development và demo

**Nhược điểm:**
- ❌ Performance thấp hơn MySQL
- ❌ Không hỗ trợ concurrent users tốt
- ❌ Khó scale cho production

### Option 2: MySQL (Production)

```env
# Cấu hình MySQL Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=xuandat_ai
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

**Ưu điểm:**
- ✅ Performance cao
- ✅ Hỗ trợ concurrent users
- ✅ ACID compliance
- ✅ Dễ scale và monitor

**Nhược điểm:**
- ❌ Cần cài đặt MySQL server
- ❌ Setup phức tạp hơn

## 🚀 Cách chuyển đổi

### Từ SQLite sang MySQL:

1. **Cài đặt MySQL:**
```bash
# Ubuntu/Debian
sudo apt install mysql-server

# CentOS/RHEL
sudo yum install mysql-server

# Windows
# Download từ: https://dev.mysql.com/downloads/mysql/
```

2. **Cập nhật config.env:**
```env
# Thay đổi từ:
DATABASE_PATH=data/database/xuandat_ai.db

# Thành:
DB_HOST=localhost
DB_PORT=3306
DB_NAME=xuandat_ai
DB_USERNAME=root
DB_PASSWORD=your_password
```

3. **Setup MySQL database:**
```bash
cd src/php-backend
php setup-mysql.php
```

4. **Test kết nối:**
```bash
php test-mysql.php
```

### Từ MySQL sang SQLite:

1. **Cập nhật config.env:**
```env
# Thay đổi từ:
DB_HOST=localhost
DB_PORT=3306
DB_NAME=xuandat_ai
DB_USERNAME=root
DB_PASSWORD=your_password

# Thành:
DATABASE_PATH=data/database/xuandat_ai.db
```

2. **Cập nhật Database.php:**
```php
// Thay đổi từ MySQL connection
$dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

// Thành SQLite connection
$dsn = "sqlite:" . $this->db_name;
```

## 🔑 Cấu hình API

### Key4U API (Hiện tại):
```env
KEY4U_API_KEY=sk-MLUnOdJqvtoK6tAIIQY6yVoGpsctz0CRzPoQED6vLpIiCzay
```

### Yescale API (Cũ):
```env
YESCALE_API_KEY=your_yescale_api_key_here
```

## 📁 Cấu hình File Upload

```env
# Cấu hình uploads
UPLOAD_PATH=data/uploads/
MAX_FILE_SIZE=10MB
```

## 🌐 Cấu hình Server

```env
# Cấu hình server
SERVER_PORT=8001
DEBUG_MODE=true
```

## 🧪 Test cấu hình

### Test API:
```bash
cd src/php-backend
php test-key4u.php
```

### Test Database:
```bash
# MySQL
php test-mysql.php

# SQLite (nếu sử dụng)
# Database sẽ tự động tạo khi chạy
```

## 🚨 Troubleshooting

### Lỗi MySQL connection:
1. Kiểm tra MySQL server đang chạy
2. Kiểm tra username/password
3. Kiểm tra database đã tồn tại
4. Chạy `php setup-mysql.php`

### Lỗi API:
1. Kiểm tra API key trong config.env
2. Kiểm tra kết nối internet
3. Kiểm tra API endpoint

### Lỗi file upload:
1. Kiểm tra quyền ghi thư mục uploads
2. Kiểm tra MAX_FILE_SIZE
3. Kiểm tra PHP upload settings

## 💡 Khuyến nghị

- **Development**: Sử dụng SQLite
- **Production**: Sử dụng MySQL
- **Demo**: Sử dụng SQLite
- **High Traffic**: Sử dụng MySQL với connection pooling
