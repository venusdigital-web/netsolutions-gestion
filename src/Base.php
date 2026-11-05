<?php
declare(strict_types=1);

namespace App;

use PDO;

/** Base SQLite de l'application, créée avec ses comptes de démonstration au premier accès. */
final class Base
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $fichier = Config::get('base');
            $nouvelle = !is_file($fichier);
            self::$pdo = new PDO('sqlite:' . $fichier, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            self::$pdo->exec('PRAGMA foreign_keys = ON');
            if ($nouvelle) {
                self::creer(self::$pdo);
            }
        }

        return self::$pdo;
    }

    private static function creer(PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE utilisateurs (
            id INTEGER PRIMARY KEY,
            courriel TEXT NOT NULL UNIQUE,
            mot_de_passe TEXT NOT NULL,
            nom TEXT NOT NULL,
            role TEXT NOT NULL CHECK (role IN ('admin', 'client'))
        )");
        $pdo->exec("CREATE TABLE demandes (
            id INTEGER PRIMARY KEY,
            utilisateur_id INTEGER NOT NULL REFERENCES utilisateurs (id),
            titre TEXT NOT NULL,
            description TEXT NOT NULL,
            justificatif_nom TEXT,
            justificatif_fichier TEXT,
            statut TEXT NOT NULL DEFAULT 'nouvelle',
            cree_le TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");

        $compte = $pdo->prepare('INSERT INTO utilisateurs (courriel, mot_de_passe, nom, role) VALUES (?, ?, ?, ?)');
        $compte->execute(['admin@netsolutions.test', password_hash('Admin2026!', PASSWORD_DEFAULT), 'Claire Martin (support NetSolutions)', 'admin']);
        $compte->execute(['client@petitfournil.test', password_hash('Client2026!', PASSWORD_DEFAULT), 'Boulangerie Le Petit Fournil', 'client']);
        $client = (int) $pdo->lastInsertId();

        $demande = $pdo->prepare('INSERT INTO demandes (utilisateur_id, titre, description, statut) VALUES (?, ?, ?, ?)');
        $demande->execute([$client, 'Caisse enregistreuse bloquée', 'La caisse affiche « Erreur 0x41 » au démarrage depuis ce matin.', 'traitée']);
        $demande->execute([$client, 'Imprimante de tickets hors ligne', 'L\'imprimante n\'apparaît plus dans la liste des périphériques.', 'nouvelle']);
    }
}
