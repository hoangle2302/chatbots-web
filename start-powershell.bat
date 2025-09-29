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
taskkill /f /im python.exe >nul 2>&1
timeout /t 2 /nobreak >nul

echo.
echo 🚀 Khởi động Backend PHP...
start "Backend PHP" powershell -Command "cd '%~dp0src\php-backend'; php -S 127.0.0.1:8000 -t ."
timeout /t 3 /nobreak >nul

echo.
echo 🌐 Khởi động Frontend...
start "Frontend PHP" powershell -Command "cd '%~dp0src\web'; php -S 127.0.0.1:8001 -t ."
timeout /t 3 /nobreak >nul

echo.
echo 📱 Mở trình duyệt...
timeout /t 2 /nobreak >nul
start http://127.0.0.1:8001/index.html

echo.
echo ✅ Hệ thống đã khởi động thành công!
echo.
echo 🌐 Frontend: http://127.0.0.1:8001/index.html
echo 📝 Register: http://127.0.0.1:8001/register.html
echo 🔐 Login:    http://127.0.0.1:8001/login.html
echo 🔧 Backend:  http://127.0.0.1:8000/test-simple.php
echo.
echo 🤖 AI Models Available:
echo   - GPT-4 Turbo ✅
echo   - Claude 3.5 Sonnet ✅
echo   - Gemini Pro ✅
echo   - DeepSeek V3 ✅
echo   - Và nhiều models khác...
echo.
echo 📋 API Endpoints:
echo   - POST http://127.0.0.1:8000/api/auth-register.php
echo   - POST http://127.0.0.1:8000/api/auth-login.php
echo   - POST http://127.0.0.1:8000/api/chat-real.php (AI thật)
echo.
echo 💡 Chat với AI models thật ngay bây giờ!
echo.
pause

