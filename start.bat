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
start "Backend Server" cmd /k "cd /d %~dp0src\php-backend && php -S 127.0.0.1:8000 router.php"
timeout /t 3 /nobreak >nul

echo.
echo Khoi dong Frontend Server (127.0.0.1:8001)...
start "Frontend Server" cmd /k "cd /d %~dp0src\web && php -S 127.0.0.1:8001"
timeout /t 3 /nobreak >nul

echo.
echo Kiem tra ket noi...
timeout /t 2 /nobreak >nul

echo.
echo Mo trinh duyet...
start http://127.0.0.1:8001/index.html

echo.
echo ========================================
echo    DA KHOI DONG THANH CONG!
echo ========================================
echo.
echo Frontend: http://127.0.0.1:8001/index.html
echo Backend:  http://127.0.0.1:8000/api/health
echo.
echo Cac trang chinh:
echo - Trang chu: http://127.0.0.1:8001/index.html
echo - Dang nhap: http://127.0.0.1:8001/login.html
echo - Dashboard: http://127.0.0.1:8001/dashboard.html
echo - Admin:     http://127.0.0.1:8001/admin-login.html
echo.
echo API Endpoints:
echo - Models:    http://127.0.0.1:8000/api/models
echo - Chat:      http://127.0.0.1:8000/api/chat-real
echo - Health:    http://127.0.0.1:8000/api/health
echo.
echo.
echo Neu co loi, vui long kiem tra:
echo - PHP da duoc cai dat chua?
echo - Port 8000 va 8001 co bi chiem chua?
echo - Firewall co chan khong?
echo - Database MySQL da chay chua?
echo.
pause
