@echo off
chcp 65001 >nul
title Thu Vien AI - Starter

echo.
echo ========================================
echo    THU VIEN AI - STARTER
echo ========================================
echo.

echo Kiem tra PHP...
php --version >nul 2>&1
if errorlevel 1 (
    echo PHP khong duoc cai dat hoac khong co trong PATH
    echo Vui long cai dat PHP va them vao PATH
    echo Download: https://windows.php.net/download/
    pause
    exit /b 1
)
echo PHP da san sang

echo.
echo Kiem tra Python...
python --version >nul 2>&1
if errorlevel 1 (
    echo Python khong duoc cai dat hoac khong co trong PATH
    echo Vui long cai dat Python 3.10+ va them vao PATH
    echo Download: https://www.python.org/downloads/
    pause
    exit /b 1
)
echo Python da san sang

echo.
echo Kiem tra thu muc du an...
if not exist "src\php-backend" (
    echo Thu muc src\php-backend khong ton tai
    pause
    exit /b 1
)
if not exist "src\web" (
    echo Thu muc src\web khong ton tai
    pause
    exit /b 1
)
echo Thu muc du an OK

echo.
echo Dung cac tien trinh PHP cu...
taskkill /f /im php.exe >nul 2>&1
timeout /t 1 /nobreak >nul

echo.
echo Khoi dong Backend Server (127.0.0.1:8000)...
start "Backend Server" cmd /k "cd /d %~dp0src\php-backend && php -d upload_max_filesize=64M -d post_max_size=64M -d memory_limit=256M -S 127.0.0.1:8000 router.php"
timeout /t 3 /nobreak >nul

echo.
echo Khoi dong AI Tool Service (127.0.0.1:8001)...
start "AI Tool Service" cmd /k "cd /d %~dp0src\php-backend\tools\AI tool && python -m uvicorn main:app --host 127.0.0.1 --port 8001 --reload"
timeout /t 3 /nobreak >nul

echo.
echo Khoi dong Frontend Server (127.0.0.1:8002)...
start "Frontend Server" cmd /k "cd /d %~dp0src\web && php -S 127.0.0.1:8002"
timeout /t 3 /nobreak >nul

echo.
echo Kiem tra ket noi...
timeout /t 2 /nobreak >nul

echo.
echo Mo trinh duyet...
start http://127.0.0.1:8002/index.html

echo.
echo ========================================
echo    DA KHOI DONG THANH CONG!
echo ========================================
echo.
echo Frontend: http://127.0.0.1:8002/index.html
echo Backend:  http://127.0.0.1:8000/api/health
echo AI Tool:  http://127.0.0.1:8001/docs
echo.
echo Cac trang chinh:
echo - Trang chu: http://127.0.0.1:8002/index.html
echo - Dang nhap: http://127.0.0.1:8002/login.html
echo - Dashboard: http://127.0.0.1:8002/dashboard.html
echo - Admin:     http://127.0.0.1:8002/admin-login.html
echo.
echo API Endpoints:
echo - Models:    http://127.0.0.1:8000/api/models
echo - Chat:      http://127.0.0.1:8000/api/chat-real
echo - Health:    http://127.0.0.1:8000/api/health
echo - AI Tool:   http://127.0.0.1:8000/api/ai-tool (POST)
echo.
echo.
echo Neu co loi, vui long kiem tra:
echo - PHP da duoc cai dat chua?
echo - Port 8000 va 8001 co bi chiem chua?
echo - Firewall co chan khong?
echo - Database MySQL da chay chua?
echo.
pause
