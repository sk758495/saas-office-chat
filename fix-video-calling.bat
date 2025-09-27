@echo off
echo ========================================
echo Video Calling System Fix Script
echo ========================================

echo.
echo 1. Installing Node.js dependencies...
npm install ws express cors

echo.
echo 2. Checking if WebSocket server is running...
netstat -an | findstr :6001 > nul
if %errorlevel% == 0 (
    echo WebSocket server is already running on port 6001
) else (
    echo WebSocket server is not running. Starting it now...
    start "WebSocket Server" cmd /k "node websocket-server.js"
    timeout /t 3 > nul
)

echo.
echo 3. Running Laravel migrations...
php artisan migrate

echo.
echo 4. Clearing Laravel cache...
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo.
echo 5. Building assets...
npm run build

echo.
echo ========================================
echo Fix completed! 
echo.
echo Next steps:
echo 1. Open http://localhost:8000/video-call-test.html to test
echo 2. Make sure your browser allows camera/microphone access
echo 3. Check that WebSocket server is running on port 6001
echo ========================================
pause