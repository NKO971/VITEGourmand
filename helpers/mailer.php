<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ROOT_PATH . 'vendor/autoload.php';

/**
 * Configure la connexion SMTP (Mailtrap) sur une instance PHPMailer,
 * à partir des variables d'environnement (voir .env / .env.example).
 * Mutualisé pour éviter la duplication entre les différentes fonctions d'envoi.
 */
function configureMailerSmtp(PHPMailer $mail): void {
    $mail->isSMTP();
    $mail->Host       = getenv('MAILTRAP_HOST') ?: 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('MAILTRAP_USERNAME');
    $mail->Password   = getenv('MAILTRAP_PASSWORD');
    $mail->Port       = (int)(getenv('MAILTRAP_PORT') ?: 2525);
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom('no-reply@vitegourmand.fr', 'VITEGourmand - Service Client');
}

function sendOrderConfirmationNotification(string $toEmail, string $clientName, string $orderNumber, string $dateDate, string $totalPrice): bool {
    $mail = new PHPMailer(true);

    try {
        configureMailerSmtp($mail);
        $mail->addAddress($toEmail, $clientName);

        $mail->isHTML(true);
        $mail->Subject = "Confirmation de votre commande N° " . $orderNumber;

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;'>
            <h2 style='color: #28a745; border-bottom: 2px solid #28a745; padding-bottom: 10px;'>
                Commande confirmée !
            </h2>
            <p>Bonjour <strong>" . htmlspecialchars($clientName) . "</strong>,</p>
            <p>Votre commande <strong>N° {$orderNumber}</strong> a bien été enregistrée.</p>
            
            <div style='background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 12px 15px; margin: 15px 0;'>
                <p style='margin: 0;'><strong>Date de la prestation :</strong> {$dateDate}</p>
                <p style='margin: 0;'><strong>Montant total :</strong> {$totalPrice} €</p>
            </div>

            <p>Vous pouvez suivre l'état de votre commande depuis votre espace client.</p>
            
            <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
            <p style='font-size: 12px; color: #777;'>
                Cet e-mail est généré automatiquement par l'application VITEGourmand.
            </p>
        </div>
        ";

        $mail->AltBody = "Bonjour {$clientName},\n\nVotre commande N° {$orderNumber} a bien été enregistrée.\nDate de la prestation : {$dateDate}\nMontant total : {$totalPrice} €\n\nCordialement,\nL'équipe VITEGourmand";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Échec envoi mail confirmation commande N° {$orderNumber} : " . $mail->ErrorInfo);
        return false;
    }
}

// Notification par e-mail pour la création d'un compte employé (sans le mot de passe pour des raisons de sécurité)
function sendEmployeeAccountCreatedNotification(string $toEmail): bool {
    $mail = new PHPMailer(true);

    try {
        configureMailerSmtp($mail);
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = "Votre compte VITEGourmand a été créé";

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;'>
            <h2 style='color: #28a745; border-bottom: 2px solid #28a745; padding-bottom: 10px;'>
                Bienvenue dans l'équipe VITEGourmand
            </h2>
            <p>Bonjour,</p>
            <p>Un compte employé a été créé pour vous sur l'espace de gestion VITEGourmand.</p>
            <p><strong>Identifiant de connexion :</strong> " . htmlspecialchars($toEmail) . "</p>
            <p>Pour obtenir votre mot de passe, merci de vous rapprocher de votre administrateur.</p>
            
            <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
            <p style='font-size: 12px; color: #777;'>
                Cet e-mail est généré automatiquement par l'application VITEGourmand.
            </p>
        </div>
        ";

        $mail->AltBody = "Bonjour,\n\nUn compte employé a été créé pour vous.\nIdentifiant : {$toEmail}\nMerci de vous rapprocher de votre administrateur pour obtenir votre mot de passe.\n\nCordialement,\nL'équipe VITEGourmand";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Échec envoi mail création employé : " . $mail->ErrorInfo);
        return false;
    }
}

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
    $mail = new PHPMailer(true);

    try {
        configureMailerSmtp($mail);
        $mail->addAddress($toEmail, $clientName);

        $mail->isHTML(true);
        $mail->Subject = "Restitution de matériel - Commande N° " . $orderNumber;

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

        $mail->AltBody = "Bonjour {$clientName},\n\nVotre commande N° {$orderNumber} contient du matériel prêté.\nDate limite de restitution : {$deadlineDate} (10 jours ouvrés).\nPassé ce délai, une pénalité de 600,00 € s'applique (CGV).\n\nCordialement,\nL'équipe VITEGourmand";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Échec envoi mail commande N° {$orderNumber} : " . $mail->ErrorInfo);
        return false;
    }
}

function sendOrderCancellationNotification(string $toEmail, string $clientName, string $orderNumber, string $motif): bool {
    $mail = new PHPMailer(true);

    try {
        configureMailerSmtp($mail);
        $mail->addAddress($toEmail, $clientName);

        $mail->isHTML(true);
        $mail->Subject = "Annulation de votre commande N° " . $orderNumber;

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;'>
            <h2 style='color: #c9302c; border-bottom: 2px solid #c9302c; padding-bottom: 10px;'>
                Information concernant votre commande
            </h2>
            <p>Bonjour <strong>" . htmlspecialchars($clientName) . "</strong>,</p>
            <p>Nous vous informons que votre commande <strong>N° {$orderNumber}</strong> a été annulée.</p>
            <p><strong>Motif de l'annulation :</strong></p>
            
            <div style='background-color: #f8f9fa; border-left: 4px solid #c9302c; padding: 12px 15px; margin: 15px 0; font-style: italic;'>
                " . nl2br(htmlspecialchars($motif)) . "
            </div>

            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            
            <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
            <p style='font-size: 12px; color: #777;'>
                Cet e-mail est généré automatiquement par l'application VITEGourmand.
            </p>
        </div>
        ";

        $mail->AltBody = "Bonjour {$clientName},\n\nVotre commande N° {$orderNumber} a été annulée.\nMotif d'annulation : {$motif}\n\nCordialement,\nL'équipe VITEGourmand";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Échec envoi mail annulation N° {$orderNumber} : " . $mail->ErrorInfo);
        return false;
    }
}