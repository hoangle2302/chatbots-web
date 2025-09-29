@echo off
chcp 65001 >nul
title Thư Viện AI - Quick Start

echo.
echo ╔══════════════════════════════════════════════════════════╗
echo ║                    THƯ VIỆN AI                          ║
echo ║                   QUICK START                          ║
echo ╚══════════════════════════════════════════════════════════╝
echo.

echo 🛑 Dừng processes cũ...
taskkill /f /im php.exe >nul 2>&1
timeout /t 1 /nobreak >nul

echo 🚀 Khởi động hệ thống...
start "Backend" powershell -Command "cd '%~dp0src\php-backend'; php -S 127.0.0.1:8000 -t ."
start "Frontend" powershell -Command "cd '%~dp0src\web'; php -S 127.0.0.1:8001 -t ."

echo ⏳ Đợi servers khởi động...
timeout /t 4 /nobreak >nul

echo 📱 Mở trình duyệt...
start http://127.0.0.1:8001/index.html

echo.
echo ✅ Hệ thống đã sẵn sàng!
echo 🌐 Truy cập: http://127.0.0.1:8001
echo 🤖 Chat với AI models thật: GPT-4, Claude, Gemini...
echo.
echo Nhấn phím bất kỳ để đóng...
pause >nul
