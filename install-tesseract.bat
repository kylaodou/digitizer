@echo off
echo =========================================
echo   INSTALLATION AUTOMATIQUE TESSERACT OCR
echo =========================================
echo.

echo Verification si Tesseract est deja installe...
tesseract --version >nul 2>&1
if %errorlevel% == 0 (
    echo ✅ Tesseract OCR est deja installe !
    pause
    exit /b 0
)

echo ❌ Tesseract OCR n'est pas installe.
echo.
echo 🚀 INSTALLATION AUTOMATIQUE...
echo.

echo 1. Telechargement de Tesseract OCR...
powershell -Command "Invoke-WebRequest -Uri 'https://github.com/UB-Mannheim/tesseract/releases/download/v5.3.3.20231005/tesseract-ocr-w64-setup-5.3.3.20231005.exe' -OutFile 'tesseract-installer.exe'"

if exist tesseract-installer.exe (
    echo ✅ Telechargement reussi !
    echo.
    echo 2. Lancement de l'installation...
    echo    ⚠️  IMPORTANT : Cochez "Additional language data (French)" !
    echo.
    pause
    tesseract-installer.exe

    echo.
    echo 3. Configuration du PATH...
    setx PATH "%PATH%;C:\Program Files\Tesseract-OCR" /M

    echo.
    echo 4. Nettoyage...
    del tesseract-installer.exe

    echo.
    echo ✅ INSTALLATION TERMINEE !
    echo.
    echo 🔄 Redemarrez votre serveur Laravel :
    echo    1. Arretez avec Ctrl+C
    echo    2. Relancez : php artisan serve
    echo.
    echo 🎯 Testez ensuite votre OCR !

) else (
    echo ❌ Erreur de telechargement.
    echo 💡 Installez manuellement depuis :
    echo    https://github.com/UB-Mannheim/tesseract/wiki
)

pause
