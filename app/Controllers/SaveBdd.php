<?php

namespace App\Controllers;

class SaveBdd extends BaseController
{
    public function saveBdd_()
    {
        $dbHost = 'localhost';
        $dbUser = 'postgres';
        $dbPass = 'root';
        $dbName = 'AR';

        $timestamp = date('Ymd_His');
        $folder = "D:/save_bdd";
        if (!is_dir($folder)) {  // si le dossier n'existe pas, on le crée
            if (!mkdir($folder, 0777, true)) {
                die("❌ Impossible de créer le dossier {$folder}");
            }
        }
        $outputFile = "{$folder}/backup_{$dbName}_{$timestamp}.sql";

        // 👉 Chemin complet vers pg_dump.exe
        $pgDump = '"C:\\Program Files\\PostgreSQL\\17\\bin\\pg_dump.exe"';

        // 👉 Mettre le mot de passe dans l'environnement
        putenv("PGPASSWORD={$dbPass}");

        // 👉 Construire la commande sans escapeshellarg pour les options
        $cmd = $pgDump
            . " -h {$dbHost}"
            . " -U {$dbUser}"
            . " -F p"
            . " -d {$dbName}"
            . " -f \"{$outputFile}\"";  // utiliser -f au lieu de `>`

        // 🔧 Exécution
        exec($cmd, $output, $return_var);

        if ($return_var === 0) {
            echo "✅ Sauvegarde réussie : {$outputFile}\n";
        } else {
            echo "❌ Erreur pendant la sauvegarde (code {$return_var}).\n";
            echo "Commande exécutée : {$cmd}\n";
            echo "Sortie : " . implode("\n", $output) . "\n";
        }
    }


    public function restoreBdd($backupFile)
    {
        $dbHost = 'localhost';
        $dbUser = 'postgres';
        $dbPass = 'root';
        $testDbName = 'ar_test';

        if (!file_exists($backupFile)) {
            die("❌ Le fichier de sauvegarde n'existe pas : {$backupFile}");
        }

        $psql = '"C:\\Program Files\\PostgreSQL\\17\\bin\\psql.exe"';

        putenv("PGPASSWORD={$dbPass}");

        // DROP
        $dropDbCmd = $psql
            . " -h {$dbHost} -U {$dbUser} -d postgres -c \"DROP DATABASE IF EXISTS \\\"{$testDbName}\\\";\"";
        exec($dropDbCmd, $output, $return_var);
        if ($return_var !== 0) {
            echo "❌ Erreur pendant la suppression de {$testDbName}.\n";
            return false;
        }
        echo "✅ Base {$testDbName} supprimée (si elle existait).\n";

        // CREATE
        $createDbCmd = $psql
            . " -h {$dbHost} -U {$dbUser} -d postgres -c \"CREATE DATABASE \\\"{$testDbName}\\\";\"";
        exec($createDbCmd, $output, $return_var);
        if ($return_var !== 0) {
            echo "❌ Erreur pendant la création de {$testDbName}.\n";
            return false;
        }
        echo "✅ Base {$testDbName} créée.\n";

        // RESTORE
        $restoreCmd = $psql
            . " -h {$dbHost} -U {$dbUser} -d {$testDbName} -f \"{$backupFile}\"";
        exec($restoreCmd, $output, $return_var);
        if ($return_var !== 0) {
            echo "❌ Erreur pendant la restauration de {$testDbName}.\n";
            return false;
        }

        echo "✅ Restauration terminée dans {$testDbName}.\n";
        return true;
    }

    public function backupBddAndRestore()
    {
        $dbHost = 'localhost';
        $dbUser = 'postgres';
        $dbPass = 'root';
        $dbName = 'AR';

        $timestamp = date('Ymd_His');
        $folder = "D:/save_bdd";
        if (!is_dir($folder)) {
            if (!mkdir($folder, 0777, true)) {
                die("❌ Impossible de créer le dossier {$folder}");
            }
        }

        $outputFile = "{$folder}/backup_{$dbName}_{$timestamp}.sql";

        $pgDump = '"C:\\Program Files\\PostgreSQL\\17\\bin\\pg_dump.exe"';

        putenv("PGPASSWORD={$dbPass}");

        $cmd = $pgDump
            . " -h {$dbHost}"
            . " -U {$dbUser}"
            . " -F p"
            . " -d {$dbName}"
            . " -f \"{$outputFile}\"";

        exec($cmd, $output, $return_var);

        if ($return_var === 0) {
            echo "✅ Sauvegarde réussie : {$outputFile}\n";
            // 👉 Appel à la fonction de restauration
            $this->restoreBdd($outputFile);
        } else {
            echo "❌ Erreur pendant la sauvegarde (code {$return_var}).\n";
            echo "Commande exécutée : {$cmd}\n";
            echo "Sortie : " . implode("\n", $output) . "\n";
        }
    }
}
