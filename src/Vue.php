<?php
declare(strict_types=1);

namespace App;

/** Échappement et gabarit commun des pages. */
final class Vue
{
    /** Texte sûr à insérer dans le HTML (évite l'injection de code dans la page). */
    public static function e(?string $texte): string
    {
        return htmlspecialchars((string) $texte, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function entete(string $titre): void
    {
        $utilisateur = Auth::utilisateur();
        echo '<!doctype html><html lang="fr"><head><meta charset="utf-8">',
            '<meta name="viewport" content="width=device-width, initial-scale=1">',
            '<title>', self::e($titre), ' — ', self::e(Config::get('nom')), '</title>',
            '<link rel="stylesheet" href="/style.css"></head><body>',
            '<header><p class="marque">', self::e(Config::get('nom')), '</p>';
        if ($utilisateur !== null) {
            echo '<nav><a href="/demandes.php">Mes demandes</a>';
            if ($utilisateur['role'] === 'admin') {
                echo ' <a href="/admin/index.php">Administration</a>';
            }
            echo ' <a href="/deconnexion.php">Se déconnecter (', self::e($utilisateur['nom']), ')</a></nav>';
        }
        echo '</header><main><h1>', self::e($titre), '</h1>';
    }

    public static function pied(): void
    {
        echo '</main></body></html>';
    }
}
