<?php
declare(strict_types=1);

namespace App;

/** Demandes d'intervention déposées par les clients. */
final class Demandes
{
    public static function creer(int $utilisateurId, string $titre, string $description, ?array $justificatif): int
    {
        $requete = Base::pdo()->prepare(
            'INSERT INTO demandes (utilisateur_id, titre, description, justificatif_nom, justificatif_fichier) VALUES (?, ?, ?, ?, ?)'
        );
        $requete->execute([$utilisateurId, $titre, $description, $justificatif['nom'] ?? null, $justificatif['fichier'] ?? null]);

        return (int) Base::pdo()->lastInsertId();
    }

    public static function pourUtilisateur(int $utilisateurId): array
    {
        $requete = Base::pdo()->prepare('SELECT * FROM demandes WHERE utilisateur_id = ? ORDER BY id DESC');
        $requete->execute([$utilisateurId]);

        return $requete->fetchAll();
    }

    public static function toutes(): array
    {
        return Base::pdo()
            ->query('SELECT d.*, u.nom AS client FROM demandes d JOIN utilisateurs u ON u.id = d.utilisateur_id ORDER BY d.id DESC')
            ->fetchAll();
    }

    public static function trouver(int $id): ?array
    {
        $requete = Base::pdo()->prepare('SELECT * FROM demandes WHERE id = ?');
        $requete->execute([$id]);

        return $requete->fetch() ?: null;
    }
}
