@echo off
echo Starting WebSocket Server for Production...
echo.
echo Make sure Node.js is installed and dependencies are available
echo.

cd /d "%~dp0"

echo Installing dependencies...
npm install ws express cors

echo.
echo Starting WebSocket server on port 6001...
echo Server will be available at: ws://emplora.jashmainfosoft.com:6001/ws
echo HTTP broadcast endpoint: http://emplora.jashmainfosoft.com:6001/broadcast
echo.

node websocket-server.js

pause