<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Notification par e-mail pour le retour de matériel sous 10 jours ouvrés.
 *
 * @param string $toEmail      Adresse e-mail du client
 * @param string $clientName   Prénom et Nom du client
 * @param string $orderNumber  Numéro/ID de la commande
 * @param string $deadlineDate Date limite de restitution (ex: "25/08/2026")
 * @return bool True si envoyé, False si échec
 */
function sendEquipmentReturnNotification(string $toEmail, string $clientName, string $orderNumber, string $deadlineDate): bool {
    // Instanciation avec 'true' pour activer la levée d'exceptions en cas d'erreur
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP (Mailtrap)
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = '6b2a318f09623a';
        $mail->Password   = '473a4af677f3ae473a4af677f3ae';
        $mail->Port       = 2525;
        $mail->CharSet    = 'UTF-8';

        // Expéditeur et Destinataire
        $mail->setFrom('no-reply@vitegourmand.fr', 'VITEGourmand - Service Client');
        $mail->addAddress($toEmail, $clientName);

        // Contenu de l'e-mail
        $mail->isHTML(true);
        $mail->Subject = "Restitution de matériel - Commande N° " . $orderNumber;

        // Template HTML
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;'>
            <h2 style='color: #c9302c; border-bottom: 2px solid #c9302c; padding-bottom: 10px;'>
                Rappel important concernant votre prestation
            </h2>
            <p>Bonjour <strong>" . htmlspecialchars($clientName) . "</strong>,</p>
            <p>Votre commande <strong>N° {$orderNumber}</strong> contient du matériel qui vous a été prêté par <strong>VITEGourmand</strong>.</p>
            <p>Conformément à nos Conditions Générales de Vente (CGV), nous vous rappelons que ce matériel doit nous être restitué au plus tard le :</p>
            
            <div style='text-align: center; margin: 20px 0;'>
                <span style='font-size: 18px; font-weight: bold; color: #8a6d3b; background-color: #fcf8e3; border: 1px solid #faebcc; padding: 12px 20px; border-radius: 5px; display: inline-block;'>
                    📅 Date limite de remise : {$deadlineDate}
                </span>
            </div>

            <p style='color: #a94442; background-color: #f2dede; border: 1px solid #ebccd1; padding: 10px; border-radius: 4px;'>
                ⚠️ <strong>Pénalité applicative :</strong> Passé le délai de 10 jours ouvrés, une facture forfaitaire de <strong>600,00 €</strong> vous sera attribuée pour non-restitution du matériel.
            </p>

            <p>Pour convenir d'un rendez-vous de restitution, merci de répondre directement à cet e-mail ou de contacter notre service logistique par téléphone.</p>
            
            <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
            <p style='font-size: 12px; color: #777;'>
                Cet e-mail est généré automatiquement par l'application VITEGourmand.
            </p>
        </div>
        ";

        // Version texte pour les clients qui ne peuvent pas lire les e-mails HTML
        $mail->AltBody = "Bonjour {$clientName},\n\nVotre commande N° {$orderNumber} contient du matériel prêté.\nDate limite de restitution : {$deadlineDate} (10 jours ouvrés).\nPassé ce délai, une pénalité de 600,00 € s'applique (CGV).\n\nCordialement,\nL'équipe VITEGourmand";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Échec envoi mail commande N° {$orderNumber} : " . $mail->ErrorInfo);
        return false;
    }
}