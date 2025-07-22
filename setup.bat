@echo off
echo Setting up POS System...
echo.

if exist .env (
    echo .env file already exists!
    echo If you want to recreate it, delete the .env file first.
    pause
    exit
)

echo Copying environment configuration...
copy env-simple.txt .env

echo.
echo Setup complete! 
echo.
echo Next steps:
echo 1. Edit .env file and update database settings
echo 2. Visit http://localhost/pos/setup in your browser
echo 3. Follow the setup wizard
echo.
pause 