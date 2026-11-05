<?php
declare(strict_types=1);

namespace App;

/** Accès aux réglages de src/reglages.php. */
final class Config
{
    private static ?array $valeurs = null;

    public static function get(string $cle): mixed
    {
        self::$valeurs ??= require __DIR__ . '/reglages.php';

        return self::$valeurs[$cle];
    }
}
