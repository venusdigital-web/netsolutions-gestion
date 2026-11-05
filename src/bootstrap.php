<?php
// Chargé en premier par chaque page de public/ : classes (Composer), puis session.
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Nom de session laissé par défaut (réglage session.name) : PHP enregistre l'avancement d'un envoi
// AVANT d'exécuter le script, en cherchant le cookie qui porte ce nom-là. Un session_name() ici
// rendrait la barre de progression muette.
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();
