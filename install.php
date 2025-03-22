<?php

// Script d'installation pour SYNCHROGEST-EDUCATION
echo "Installation de SYNCHROGEST-EDUCATION...\n";

// Vérifier l'existence de la base de données
if (!file_exists(__DIR__.'/database/database.sqlite')) {
    echo "Création de la base de données...\n";
    file_put_contents(__DIR__.'/database/database.sqlite', '');
}

// Exécuter les migrations
echo "Configuration de la base de données...\n";
shell_exec('php artisan migrate --force');

// Créer un utilisateur par défaut pour chaque module
echo "Création des utilisateurs par défaut...\n";
shell_exec('php artisan db:seed --class=DefaultUsersSeeder');

echo "Installation terminée avec succès!\n";
echo "Vous pouvez maintenant lancer l'application avec la commande: php artisan serve\n";