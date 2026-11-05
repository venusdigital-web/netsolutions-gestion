<?php
// Réglages de l'application de gestion des demandes d'intervention de NetSolutions.
return [
    'nom' => 'NetSolutions — Demandes d\'intervention',
    'base' => __DIR__ . '/../donnees/gestion.sqlite',
    'stockage' => __DIR__ . '/../stockage',
    // Justificatifs : captures d'écran et PDF (bon de commande, message d'erreur, facture).
    'justificatif_taille_max' => 2 * 1024 * 1024,
    'justificatif_types' => ['application/pdf' => 'pdf', 'image/png' => 'png', 'image/jpeg' => 'jpg'],
    // Courriel envoyé au support à chaque nouvelle demande (désactivé : pas de serveur de messagerie).
    'notifications' => false,
    'courriel_support' => 'support@netsolutions.test',
];
