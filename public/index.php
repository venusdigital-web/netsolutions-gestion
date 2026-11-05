<?php
// Page de connexion.
require __DIR__ . '/../src/bootstrap.php';

use App\Auth;
use App\Vue;

if (Auth::utilisateur() !== null) {
    header('Location: ' . (Auth::estAdmin() ? '/admin/index.php' : '/demandes.php'));
    exit;
}

$erreur = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Auth::verifierCsrf() && Auth::connecter((string) ($_POST['courriel'] ?? ''), (string) ($_POST['mot_de_passe'] ?? ''))) {
        header('Location: ' . (Auth::estAdmin() ? '/admin/index.php' : '/demandes.php'));
        exit;
    }
    $erreur = true;
}

Vue::entete('Connexion');
if ($erreur) {
    echo '<p class="erreur" role="alert">Courriel ou mot de passe incorrect.</p>';
}
?>
<form method="post" action="/index.php">
    <input type="hidden" name="csrf" value="<?= Vue::e(Auth::jetonCsrf()) ?>">
    <p><label for="courriel">Courriel</label><br><input type="email" id="courriel" name="courriel" required autofocus></p>
    <p><label for="mot_de_passe">Mot de passe</label><br><input type="password" id="mot_de_passe" name="mot_de_passe" required></p>
    <p><button type="submit">Se connecter</button></p>
</form>
<?php
Vue::pied();
