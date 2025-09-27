@echo off
echo Starting WebSocket Server for Video Calls...
cd /d "%~dp0"
node websocket-server.js
pause