<?php
// Administration : toutes les demandes de tous les clients. Réservé au support (rôle admin).
require __DIR__ . '/../../src/bootstrap.php';

use App\Auth;
use App\Demandes;
use App\Vue;

Auth::exigerAdmin();

Vue::entete('Administration — toutes les demandes');
?>
<table>
    <thead><tr><th scope="col">N°</th><th scope="col">Date</th><th scope="col">Client</th><th scope="col">Titre</th><th scope="col">Justificatif</th><th scope="col">Statut</th></tr></thead>
    <tbody>
    <?php foreach (Demandes::toutes() as $d): ?>
        <tr>
            <td><?= (int) $d['id'] ?></td>
            <td><?= Vue::e($d['cree_le']) ?></td>
            <td><?= Vue::e($d['client']) ?></td>
            <td><?= Vue::e($d['titre']) ?></td>
            <td><?= $d['justificatif_fichier'] ? '<a href="/justificatif.php?id=' . (int) $d['id'] . '">' . Vue::e($d['justificatif_nom']) . '</a>' : '—' ?></td>
            <td><?= Vue::e($d['statut']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php
Vue::pied();
