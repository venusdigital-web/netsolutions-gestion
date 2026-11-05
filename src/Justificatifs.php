<?php
declare(strict_types=1);

namespace App;

use RuntimeException;

/**
 * Pièces jointes des demandes. Rangées dans stockage/, hors de public/ : elles ne sont jamais
 * servies directement par le serveur web, seulement par public/justificatif.php après contrôle.
 */
final class Justificatifs
{
    /**
     * Enregistre le fichier reçu dans $_FILES['justificatif'].
     * Renvoie null si aucun fichier n'a été joint, ['nom' => ..., 'fichier' => ...] sinon.
     * Lève une RuntimeException dont le message est un code d'erreur (voir public/demandes.php).
     */
    public static function enregistrer(array $fichier): ?array
    {
        if ($fichier['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (in_array($fichier['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
            || $fichier['size'] > Config::get('justificatif_taille_max')) {
            throw new RuntimeException('taille');
        }
        if ($fichier['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($fichier['tmp_name'])) {
            throw new RuntimeException('envoi');
        }

        // Type réel lu dans le contenu du fichier, pas dans son nom ni dans ce qu'annonce le navigateur.
        $type = (new \finfo(FILEINFO_MIME_TYPE))->file($fichier['tmp_name']);
        $extension = Config::get('justificatif_types')[$type] ?? null;
        if ($extension === null) {
            throw new RuntimeException('type');
        }

        $nomStocke = bin2hex(random_bytes(16)) . '.' . $extension;
        if (!move_uploaded_file($fichier['tmp_name'], Config::get('stockage') . '/' . $nomStocke)) {
            throw new RuntimeException('envoi');
        }

        return ['nom' => basename($fichier['name']), 'fichier' => $nomStocke];
    }

    public static function chemin(string $nomStocke): string
    {
        return Config::get('stockage') . '/' . basename($nomStocke);
    }
}
