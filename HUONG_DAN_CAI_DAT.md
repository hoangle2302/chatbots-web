# 🚀 Hướng dẫn cài đặt Backend PHP - Thư Viện AI

## 📋 Yêu cầu hệ thống
- Windows 10/11
- PHP 7.4+ 
- MySQL hoặc SQLite
- Composer (quản lý thư viện PHP)

## 🎯 Phương án 1: Sử dụng XAMPP (Khuyến nghị - Dễ nhất)

### Bước 1: Tải và cài đặt XAMPP
1. Truy cập: https://www.apachefriends.org/download.html
2. Tải XAMPP cho Windows (khoảng 150MB)
3. Chạy file cài đặt với quyền Administrator
4. Chọn: Apache, MySQL, PHP, phpMyAdmin
5. Cài đặt vào `C:\xampp`

### Bước 2: Khởi động XAMPP
1. Mở "XAMPP Control Panel"
2. Start **Apache** và **MySQL**
3. Mở trình duyệt: http://localhost (kiểm tra Apache)
4. Mở: http://localhost/phpmyadmin (kiểm tra MySQL)

### Bước 3: Cài đặt Composer
1. Truy cập: https://getcomposer.org/download/
2. Tải file `composer.phar`
3. Lưu vào `C:\xampp\php\composer.phar`
4. Tạo file `C:\xampp\php\composer.bat`:
   ```batch
   @php "C:\xampp\php\composer.phar" %*
   ```
5. Thêm `C:\xampp\php` vào PATH của Windows

### Bước 4: Cài đặt project
1. Copy thư mục `src/php-backend` vào `C:\xampp\htdocs\thuvien-ai`
2. Mở Command Prompt và chạy:
   ```cmd
   cd C:\xampp\htdocs\thuvien-ai
   composer install
   ```

### Bước 5: Cấu hình database
1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Tạo database mới tên `thuvien_ai`
3. Chạy file setup: `C:\xampp\htdocs\thuvien-ai\setup-mysql.php`

### Bước 6: Cấu hình môi trường
1. Copy `config.env.example` thành `config.env`
2. Cập nhật thông tin trong `config.env`:
   ```
   DB_HOST=localhost
   DB_NAME=thuvien_ai
   DB_USERNAME=root
   DB_PASSWORD=
   JWT_SECRET=your-super-secret-key-here
   KEY4U_API_KEY=your-api-key-here
   ```

### Bước 7: Khởi động server
```cmd
cd C:\xampp\htdocs\thuvien-ai
php -S localhost:8001
```

### Bước 8: Test API
```cmd
php test-api.php
```

---

## 🎯 Phương án 2: Cài đặt thủ công

### Bước 1: Cài đặt PHP
1. Tải PHP từ: https://windows.php.net/download/
2. Giải nén vào `C:\php`
3. Thêm `C:\php` vào PATH

### Bước 2: Cài đặt MySQL
1. Tải MySQL từ: https://dev.mysql.com/downloads/mysql/
2. Cài đặt MySQL Server
3. Tạo database `thuvien_ai`

### Bước 3: Cài đặt Composer
1. Tải composer.phar từ: https://getcomposer.org/download/
2. Lưu vào `C:\php\composer.phar`
3. Tạo `C:\php\composer.bat`:
   ```batch
   @php "C:\php\composer.phar" %*
   ```

### Bước 4: Cài đặt project
1. Copy `src/php-backend` vào thư mục web server
2. Chạy `composer install`

---

## 🧪 Test API sau khi cài đặt

### 1. Test đăng ký:
```bash
curl -X POST http://localhost:8001/api/auth.php?action=register \
  -H "Content-Type: application/json" \
  -d '{"username":"testuser","password":"password123"}'
```

### 2. Test đăng nhập:
```bash
curl -X POST http://localhost:8001/api/auth.php?action=login \
  -H "Content-Type: application/json" \
  -d '{"username":"testuser","password":"password123"}'
```

### 3. Test upload file:
```bash
curl -X POST http://localhost:8001/api/documents.php?action=upload \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "file=@test.txt"
```

---

## 🔧 Troubleshooting

### Lỗi "composer not found":
- Kiểm tra PATH có chứa đường dẫn đến composer
- Thử chạy: `C:\xampp\php\composer.bat --version`

### Lỗi "php not found":
- Cài đặt XAMPP hoặc PHP standalone
- Thêm PHP vào PATH

### Lỗi database connection:
- Kiểm tra MySQL đang chạy
- Kiểm tra thông tin trong `config.env`
- Chạy `setup-mysql.php`

### Lỗi JWT:
- Kiểm tra `JWT_SECRET` trong `config.env`
- Đảm bảo thư viện firebase/php-jwt đã cài đặt

---

## 📞 Hỗ trợ

Nếu gặp vấn đề, hãy:
1. Kiểm tra log trong `error.log`
2. Chạy `php test-api.php` để test
3. Kiểm tra database connection
4. Đảm bảo tất cả dependencies đã cài đặt

**Chúc bạn cài đặt thành công! 🎉**

