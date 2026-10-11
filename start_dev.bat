@echo off
start "Laravel Server" cmd /k "php artisan serve"
start "Python AI Engine" cmd /k "cd ai_microservice && venv\Scripts\activate && uvicorn main:app --host 127.0.0.1 --port 8001 --reload"