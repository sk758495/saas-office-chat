@echo off
echo Starting WebSocket Server for Production...
echo.
echo Domain: https://emplora.jashmainfosoft.com
echo Port: 6001
echo.

cd /d "c:\Users\sunnyverma\Desktop\SAAS Websites\office-chat"

echo Checking Node.js installation...
node --version
if %errorlevel% neq 0 (
    echo ERROR: Node.js is not installed or not in PATH
    echo Please install Node.js from https://nodejs.org/
    pause
    exit /b 1
)

echo.
echo Installing dependencies...
npm install ws express cors

echo.
echo Starting WebSocket server...
echo Press Ctrl+C to stop the server
echo.

node websocket-server.js

pause