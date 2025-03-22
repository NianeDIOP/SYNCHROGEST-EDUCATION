@echo off
echo Démarrage de SYNCHROGEST-EDUCATION...

REM Vérifier si c'est le premier lancement
if not exist "database\database.sqlite" (
    echo Premier lancement détecté. Installation en cours...
    php install.php
)

REM Démarrer le serveur
php artisan serve --host=0.0.0.0 --port=8000

pause