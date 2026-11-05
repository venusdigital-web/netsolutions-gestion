# NetSolutions — Demandes d'intervention

Application PHP de NetSolutions : ses clients y déposent leurs demandes d'intervention, avec un
justificatif (capture d'écran ou PDF), et le support les traite. C'est l'application fournie pour la
mise en pratique SLAM du TP « Alerte PHP2Shell » (chapitre 6).

## Lancer l'application

Dans la machine virtuelle du TP, l'application est déjà dans `~/netsolutions-gestion` :

```
cd ~/netsolutions-gestion
docker compose up -d --build
```

Puis ouvrez <http://localhost:8080> dans Firefox. Après chaque modification du code, relancez la même
commande : l'image est reconstruite avec votre code.

| Commande | Effet |
|---|---|
| `docker compose up -d --build` | construit l'image et démarre l'application |
| `docker compose logs` | affiche les journaux d'Apache et de PHP |
| `docker compose down` | arrête l'application (la base et les justificatifs sont conservés) |
| `docker compose down -v` | arrête et efface la base et les justificatifs : on repart des données de démonstration |

Sans Docker, sur un poste avec PHP 8.2 ou plus et Composer : `composer install`, puis
`php -S localhost:8080 -t public`. Le serveur intégré de PHP ne traite qu'une requête à la fois :
la barre de progression de l'envoi reste alors vide, le reste fonctionne.

## Comptes de démonstration

| Rôle | Courriel | Mot de passe |
|---|---|---|
| Support (administrateur) | `admin@netsolutions.test` | `Admin2026!` |
| Client | `client@petitfournil.test` | `Client2026!` |

La base SQLite est créée avec ces comptes au premier accès.

## Organisation du code

| Chemin | Contenu |
|---|---|
| `public/` | les seules pages accessibles par le web |
| `public/demandes.php` | demandes du client et dépôt d'une nouvelle demande avec justificatif |
| `public/progression.php`, `public/progression.js` | barre de progression de l'envoi du justificatif |
| `public/justificatif.php` | téléchargement d'un justificatif, après contrôle des droits |
| `public/admin/` | pages du support : chacune commence par `Auth::exigerAdmin()` |
| `src/` | classes de l'application (espace de noms `App\`, chargées par Composer) |
| `src/reglages.php` | réglages : taille et types de justificatifs acceptés, notifications |
| `tests/` | tests PHPUnit |
| `docker/php.ini` | réglages PHP copiés dans l'image |
| `Dockerfile`, `compose.yaml` | construction de l'image et lancement |
| `donnees/`, `stockage/` | base SQLite et justificatifs (jamais servis directement) |

## Tests

```
vendor/bin/phpunit
```

Un fichier `tests/NomTest.php` par classe testée, une méthode `test...()` par cas.
