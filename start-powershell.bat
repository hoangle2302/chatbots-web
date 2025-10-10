@echo off
chcp 65001 >nul
title Thư Viện AI - PowerShell Launcher

echo.
echo ╔══════════════════════════════════════════════════════════╗
echo ║                    THƯ VIỆN AI                          ║
echo ║                 POWERSHELL LAUNCHER                     ║
echo ╚══════════════════════════════════════════════════════════╝
echo.

echo 🛑 Dừng processes cũ...
taskkill /f /im php.exe >nul 2>&1
timeout /t 2 /nobreak >nul

echo.
echo 🗄️ Kiểm tra MySQL...
sc query MySQL80 | find "RUNNING" >nul
if errorlevel 1 (
    echo    - MySQL chưa chạy, đang khởi động...
    net start MySQL80 >nul 2>&1
    if errorlevel 1 (
        echo    ✗ Lỗi: Không thể khởi động MySQL!
        pause
        exit /b 1
    ) else (
        echo    ✓ MySQL đã được khởi động
    )
) else (
    echo    ✓ MySQL đang chạy
)

echo.
echo 🧪 Test database connection...
php test-db.php >nul 2>&1
if errorlevel 1 (
    echo    ✗ Database connection failed!
    echo    - Đang chạy test chi tiết...
    php test-db.php
    pause
    exit /b 1
) else (
    echo    ✓ Database connection OK
)

echo.
echo 🚀 Khởi động Backend PHP (Port 8000)...
start "Backend PHP" powershell -Command "cd '%~dp0src\php-backend'; php -S 127.0.0.1:8000 server.php"
timeout /t 3 /nobreak >nul

echo.
echo 🌐 Khởi động Frontend (Port 8001)...
start "Frontend" powershell -Command "cd '%~dp0src\web'; php -S 127.0.0.1:8001 -t ."
timeout /t 3 /nobreak >nul

echo.
echo 📱 Mở trình duyệt...
timeout /t 2 /nobreak >nul
start http://127.0.0.1:8001

echo.
echo ✅ Hệ thống đã khởi động thành công!
echo.
echo 🌐 Frontend: http://127.0.0.1:8001
echo 📝 Register: http://127.0.0.1:8001/register.html
echo 🔐 Login:    http://127.0.0.1:8001/login.html
echo 🔧 Admin:    http://127.0.0.1:8001/admin-dashboard.html
echo.
echo 🔧 Backend API: http://127.0.0.1:8000
echo.
echo 🤖 AI Models Available:
echo   - GPT-4 Turbo ✅
echo   - Claude 3.5 Sonnet ✅
echo   - Gemini Pro ✅
echo   - DeepSeek V3 ✅
echo   - Và nhiều models khác...
echo.
echo 📋 API Endpoints (Backend):
echo   - GET  http://127.0.0.1:8000/api/health
echo   - POST http://127.0.0.1:8000/api/auth/register
echo   - POST http://127.0.0.1:8000/api/auth/login
echo   - POST http://127.0.0.1:8000/api/chat
echo.
echo 📋 Frontend Pages:
echo   - Home:     http://127.0.0.1:8001/index.html
echo   - Register: http://127.0.0.1:8001/register.html
echo   - Login:    http://127.0.0.1:8001/login.html
echo   - Dashboard: http://127.0.0.1:8001/dashboard.html
echo.
echo 💡 Chat với AI models thật ngay bây giờ!
echo.
echo ⚠️  Lưu ý: Nhấn Ctrl+C trong cửa sổ PowerShell để dừng server
echo.
pause

