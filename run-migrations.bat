@echo off
cd /d "%~dp0"
php artisan migrate
pause

