<?php
declare(strict_types=1);

namespace App;

use PHPMailer\PHPMailer\Exception as ErreurCourriel;
use PHPMailer\PHPMailer\PHPMailer;

/** Prévient le support par courriel d'une nouvelle demande (bibliothèque PHPMailer, via Composer). */
final class Notification
{
    public static function nouvelleDemande(int $demandeId, string $titre, string $client): void
    {
        if (!Config::get('notifications')) {
            return;
        }
        $courriel = new PHPMailer(true);
        try {
            $courriel->CharSet = PHPMailer::CHARSET_UTF8;
            $courriel->setFrom('noreply@netsolutions.test', 'Gestion NetSolutions');
            $courriel->addAddress(Config::get('courriel_support'));
            $courriel->Subject = "Nouvelle demande n° $demandeId : $titre";
            $courriel->Body = "$client a déposé la demande n° $demandeId : $titre.";
            $courriel->send();
        } catch (ErreurCourriel $e) {
            // Un courriel non parti ne doit pas faire perdre la demande, déjà enregistrée.
            error_log('Notification non envoyée : ' . $courriel->ErrorInfo);
        }
    }
}
