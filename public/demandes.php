<?php
// Demandes d'intervention du client connecté, et dépôt d'une nouvelle demande avec un justificatif.
require __DIR__ . '/../src/bootstrap.php';

use App\Auth;
use App\Demandes;
use App\Justificatifs;
use App\Notification;
use App\Vue;

$utilisateur = Auth::exigerConnexion();

const MESSAGES = [
    'csrf' => 'Le formulaire a expiré : rechargez la page et recommencez.',
    'titre' => 'Le titre et la description sont obligatoires.',
    'trop_gros' => 'L\'envoi dépasse la taille maximale acceptée par le serveur.',
    'taille' => 'Le justificatif dépasse 2 Mo.',
    'type' => 'Le justificatif doit être un PDF, une image PNG ou une image JPEG.',
    'envoi' => 'Le justificatif n\'a pas pu être reçu : recommencez.',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Requête plus grosse que post_max_size : PHP l'ignore et $_POST comme $_FILES arrivent vides.
    if ($_POST === [] && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        header('Location: /demandes.php?erreur=trop_gros', true, 303);
        exit;
    }
    if (!Auth::verifierCsrf()) {
        header('Location: /demandes.php?erreur=csrf', true, 303);
        exit;
    }
    $titre = trim((string) ($_POST['titre'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    if ($titre === '' || $description === '') {
        header('Location: /demandes.php?erreur=titre', true, 303);
        exit;
    }
    try {
        $justificatif = Justificatifs::enregistrer($_FILES['justificatif'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
    } catch (RuntimeException $e) {
        header('Location: /demandes.php?erreur=' . urlencode($e->getMessage()), true, 303);
        exit;
    }
    $id = Demandes::creer($utilisateur['id'], $titre, $description, $justificatif);
    Notification::nouvelleDemande($id, $titre, $utilisateur['nom']);
    header('Location: /demandes.php?enregistree=' . $id, true, 303);
    exit;
}

// Clé de suivi de l'envoi en cours, lue par progression.php (voir public/progression.js).
$cleProgression = bin2hex(random_bytes(8));

Vue::entete('Mes demandes d\'intervention');
if (isset($_GET['enregistree'])) {
    echo '<p class="succes" role="status">Demande n° ', (int) $_GET['enregistree'], ' enregistrée : le support vous répond sous 24 h ouvrées.</p>';
}
if (isset(MESSAGES[$_GET['erreur'] ?? ''])) {
    echo '<p class="erreur" role="alert">', Vue::e(MESSAGES[$_GET['erreur']]), '</p>';
}
?>
<h2>Nouvelle demande</h2>
<form id="nouvelle-demande" method="post" action="/demandes.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= Vue::e(Auth::jetonCsrf()) ?>">
    <!-- Doit précéder le champ de fichier : PHP suit l'avancement de l'envoi sous cette clé. -->
    <input type="hidden" name="<?= Vue::e(ini_get('session.upload_progress.name')) ?>" value="<?= $cleProgression ?>">
    <p><label for="titre">Titre</label><br><input type="text" id="titre" name="titre" maxlength="120" required></p>
    <p><label for="description">Description du problème</label><br><textarea id="description" name="description" rows="5" required></textarea></p>
    <p><label for="justificatif">Justificatif (facultatif) : PDF, PNG ou JPEG, 2 Mo au maximum</label><br>
        <input type="file" id="justificatif" name="justificatif" accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg"></p>
    <p><progress id="progression" max="100" value="0" hidden></progress> <span id="progression-texte" aria-live="polite"></span></p>
    <p><button type="submit">Envoyer la demande</button></p>
</form>

<h2>Mes demandes</h2>
<?php $demandes = Demandes::pourUtilisateur($utilisateur['id']); ?>
<?php if ($demandes === []): ?>
<p>Aucune demande pour l'instant.</p>
<?php else: ?>
<table>
    <thead><tr><th scope="col">N°</th><th scope="col">Date</th><th scope="col">Titre</th><th scope="col">Justificatif</th><th scope="col">Statut</th></tr></thead>
    <tbody>
    <?php foreach ($demandes as $d): ?>
        <tr>
            <td><?= (int) $d['id'] ?></td>
            <td><?= Vue::e($d['cree_le']) ?></td>
            <td><?= Vue::e($d['titre']) ?></td>
            <td><?= $d['justificatif_fichier'] ? '<a href="/justificatif.php?id=' . (int) $d['id'] . '">' . Vue::e($d['justificatif_nom']) . '</a>' : '—' ?></td>
            <td><?= Vue::e($d['statut']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
<script src="/progression.js"></script>
<?php
Vue::pied();
