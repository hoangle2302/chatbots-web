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
echo Khoi dong Backend (127.0.0.1:8000)...
start "Backend PHP" cmd /k "cd /d %~dp0src\php-backend && php -S 127.0.0.1:8000 server.php"
timeout /t 3 /nobreak >nul

echo.
echo Khoi dong Frontend (127.0.0.1:8001)...
start "Frontend" cmd /k "cd /d %~dp0src\web && php -S 127.0.0.1:8001 -t ."
timeout /t 3 /nobreak >nul

echo.
echo Mo trinh duyet...
start http://127.0.0.1:8001/

echo.
echo Da khoi dong xong!
echo Frontend: http://127.0.0.1:8001
echo Backend:  http://127.0.0.1:8000
echo.
echo Neu co loi, vui long kiem tra:
echo - PHP da duoc cai dat chua?
echo - Port 8000 va 8001 co bi chiem chua?
echo - Firewall co chan khong?
echo.
pause
