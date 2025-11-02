@echo off
chcp 65001 >nul
title Thư Viện AI - Starter

echo.
echo ========================================
echo    THU VIEN AI - SYSTEM STARTER
echo ========================================
echo.

:: Kiểm tra PHP
php --version >nul 2>&1
if errorlevel 1 (
    echo  Error: PHP chua duoc cai dat
    pause
    exit /b 1
)
echo ✅ PHP da san sang

:: Kiểm tra Python
python --version >nul 2>&1
if errorlevel 1 (
    echo  Error: Python chua duoc cai dat
    pause
    exit /b 1
)
echo ✅ Python da san sang

:: Kiểm tra thư mục
if not exist "src\php-backend" (
    echo  Error: Thu muc du an khong hop le
    pause
    exit /b 1
)
echo ✅ Thu muc du an OK

:: Tạo virtual environment nếu chưa có
if not exist "src\php-backend\tools\AI tool\.venv" (
    echo  Error: Dang tao virtual environment...
    cd "src\php-backend\tools\AI tool"
    python -m venv .venv
    call .venv\Scripts\activate.bat
    pip install fastapi uvicorn python-multipart -q
    cd %~dp0
    echo  Success: Da tao virtual environment
)

:: Dừng process cũ
echo.
echo Dang don dep process cu...
taskkill /f /im php.exe >nul 2>&1
taskkill /f /im python.exe >nul 2>&1
timeout /t 1 /nobreak >nul

:: Khởi động Backend
echo.
echo [1/3] Khoi dong Backend Server (8000)...
start "Backend Server" cmd /k "cd /d %~dp0src\php-backend && php -d upload_max_filesize=64M -d post_max_size=64M -d memory_limit=256M -S 127.0.0.1:8000 router.php"
timeout /t 2 /nobreak >nul

:: Khởi động AI Tool
echo [2/3] Khoi dong AI Tool Service (8001)...
cd "src\php-backend\tools\AI tool"
start "AI Tool Service" cmd /k "cd /d %~dp0src\php-backend\tools\AI tool && .venv\Scripts\activate.bat && python -m uvicorn main:app --host 127.0.0.1 --port 8001 --reload"
cd %~dp0
timeout /t 2 /nobreak >nul

:: Khởi động Frontend
echo [3/3] Khoi dong Frontend Server (8002)...
start "Frontend Server" cmd /k "cd /d %~dp0src\web && php -S 127.0.0.1:8002"
timeout /t 2 /nobreak >nul

:: Mở trình duyệt
echo.
echo  Success: DA KHOI DONG THANH CONG!
echo.
echo Frontend:  http://127.0.0.1:8002
echo Backend:   http://127.0.0.1:8000/api/health.php
echo AI Tool:   http://127.0.0.1:8001/docs
echo.
timeout /t 2 /nobreak >nul
start http://127.0.0.1:8002/index.html

pause
