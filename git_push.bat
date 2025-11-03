@echo off
REM ============================================
REM Git Push Script for Corres Project
REM ============================================
REM This script stages, commits, and pushes all changes to GitHub
REM Repository: https://github.com/anziinols/corres.git
REM ============================================

echo.
echo ============================================
echo Git Push Script for Corres Project
echo ============================================
echo.

REM Check if git is installed
git --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Git is not installed or not in PATH
    echo Please install Git from https://git-scm.com/
    pause
    exit /b 1
)

echo [1/5] Checking git status...
git status
echo.

echo [2/5] Staging all changes...
git add .
if errorlevel 1 (
    echo ERROR: Failed to stage changes
    pause
    exit /b 1
)
echo SUCCESS: All changes staged
echo.

echo [3/5] Creating commit...
git commit -m "Add Dakoii Admin Portal with authentication and update system configuration

- Implemented Dakoii admin portal with dark theme
- Added session-based authentication system
- Created dakoii_users table migration
- Added DakoiiUserModel with password hashing
- Implemented DakoiiAuthFilter for route protection
- Created login and dashboard views with Bootstrap 5
- Updated base URL to http://localhost/corres/
- Moved index.php to root directory
- Updated database configuration (corres_db)
- Created landing page for public site
- Added .htaccess for URL rewriting
- Created migration and seeder scripts
- Default user: fkenny / dakoii"

if errorlevel 1 (
    echo.
    echo NOTE: Nothing to commit or commit failed
    echo This might mean all changes are already committed
    echo.
)
echo.

echo [4/5] Checking remote repository...
git remote -v
echo.

echo [5/5] Pushing to GitHub...
git push origin main
if errorlevel 1 (
    echo.
    echo Trying 'master' branch instead...
    git push origin master
    if errorlevel 1 (
        echo.
        echo ERROR: Failed to push to GitHub
        echo.
        echo Possible solutions:
        echo 1. Make sure you have internet connection
        echo 2. Verify your GitHub credentials
        echo 3. Check if the remote repository exists
        echo 4. Try: git push -u origin main
        echo.
        pause
        exit /b 1
    )
)

echo.
echo ============================================
echo SUCCESS: All changes pushed to GitHub!
echo ============================================
echo Repository: https://github.com/anziinols/corres.git
echo.
pause

