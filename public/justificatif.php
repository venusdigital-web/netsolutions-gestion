<?php
// Téléchargement d'un justificatif : réservé au client qui l'a déposé et au support.
require __DIR__ . '/../src/bootstrap.php';

use App\Auth;
use App\Demandes;
use App\Justificatifs;

$utilisateur = Auth::exigerConnexion();
$demande = Demandes::trouver((int) ($_GET['id'] ?? 0));

if ($demande === null || $demande['justificatif_fichier'] === null
    || ($demande['utilisateur_id'] !== $utilisateur['id'] && !Auth::estAdmin())) {
    http_response_code(404);
    exit('Justificatif introuvable.');
}

$chemin = Justificatifs::chemin($demande['justificatif_fichier']);
header('Content-Type: ' . (new finfo(FILEINFO_MIME_TYPE))->file($chemin));
header('Content-Length: ' . filesize($chemin));
header('Content-Disposition: attachment; filename="' . str_replace('"', '', $demande['justificatif_nom']) . '"');
header('X-Content-Type-Options: nosniff');
readfile($chemin);
