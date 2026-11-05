<?php
// Avancement de l'envoi d'un justificatif, en JSON, interrogé par progression.js pendant l'envoi.
// PHP l'écrit lui-même dans la session (réglage session.upload_progress.enabled) sous la clé
// « préfixe + valeur du champ caché » ; il l'efface dès que l'envoi est terminé.
require __DIR__ . '/../src/bootstrap.php';

use App\Auth;

Auth::exigerConnexion();

$cle = ini_get('session.upload_progress.prefix') . preg_replace('/[^a-f0-9]/', '', (string) ($_GET['cle'] ?? ''));
$avancement = $_SESSION[$cle] ?? null;
session_write_close();

header('Content-Type: application/json');
echo json_encode($avancement === null
    ? ['suivi' => false]
    : ['suivi' => true, 'recu' => $avancement['bytes_processed'], 'total' => $avancement['content_length']]);
