@echo off
echo ============================================
echo   AeroSense Dev Server (Network Accessible)
echo ============================================
echo.
echo Your current network IP addresses:
ipconfig | findstr /i "IPv4"
echo.
echo *** IMPORTANT: Always use THIS bat file ***
echo *** Never use plain "php artisan serve"  ***
echo *** Plain serve only works on localhost, ***
echo *** NOT accessible by the ESP32!         ***
echo.
echo Starting server on 0.0.0.0:8000 ...
echo (Press Ctrl+C to stop)
echo.
cd /d "C:\D\Antigravity\AerosenseV2-main"
php artisan serve --host=0.0.0.0 --port=8000
