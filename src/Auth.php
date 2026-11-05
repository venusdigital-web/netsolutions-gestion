<?php
declare(strict_types=1);

namespace App;

/** Connexion, rôles et jeton anti-CSRF des formulaires. */
final class Auth
{
    public static function connecter(string $courriel, string $motDePasse): bool
    {
        $requete = Base::pdo()->prepare('SELECT * FROM utilisateurs WHERE courriel = ?');
        $requete->execute([mb_strtolower(trim($courriel))]);
        $utilisateur = $requete->fetch();
        if (!$utilisateur || !password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['utilisateur'] = ['id' => (int) $utilisateur['id'], 'nom' => $utilisateur['nom'], 'role' => $utilisateur['role']];

        return true;
    }

    public static function utilisateur(): ?array
    {
        return $_SESSION['utilisateur'] ?? null;
    }

    public static function estAdmin(): bool
    {
        return (self::utilisateur()['role'] ?? null) === 'admin';
    }

    /** À appeler en tête de chaque page réservée aux utilisateurs connectés. */
    public static function exigerConnexion(): array
    {
        $utilisateur = self::utilisateur();
        if ($utilisateur === null) {
            header('Location: /index.php');
            exit;
        }

        return $utilisateur;
    }

    /** À appeler en tête de chaque page de public/admin/ : un client reçoit une erreur 403. */
    public static function exigerAdmin(): array
    {
        $utilisateur = self::exigerConnexion();
        if ($utilisateur['role'] !== 'admin') {
            http_response_code(403);
            exit('Accès réservé au support NetSolutions.');
        }

        return $utilisateur;
    }

    public static function deconnecter(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function jetonCsrf(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    }

    public static function verifierCsrf(): bool
    {
        return hash_equals(self::jetonCsrf(), (string) ($_POST['csrf'] ?? ''));
    }
}
