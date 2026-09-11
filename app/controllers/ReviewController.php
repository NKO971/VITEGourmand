<?php
// app/controllers/reviewController.php

require_once ROOT_PATH . 'app/models/AvisModel.php';

class ReviewController 
{
    private AvisModel $avisModel;

    public function __construct() 
    {
        // Contrôle d'accès : Employé (role_id = 2) ou Admin (role_id = 1)
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
            header('Location: ?page=connexion');
            exit();
        }

        $this->avisModel = new AvisModel();
    }

    // Affiche la liste des avis en attente de modération
    
    public function index() 
    {
        // Récupération des données depuis le modèle MongoDB
        $pendingReviews = $this->avisModel->getAvisByStatut('en_attente');

        // Rendu via BaseController en respectant l'ordre des paramètres :
        // render($title, $viewFile, $additionalCss, $additionalJs, $data, $layout)
        BaseController::render(
            "Modération des avis - VITEGourmand", 
            "reviews.view.php",                   
            [],                                   
            ['js/dashboard-avis.js'],                                   
            [                                     
                'pendingReviews' => $pendingReviews,
                'currentPage'    => 'employee_reviews'
            ],
            'back'                                
        );
    }

    // Traite les actions de modération (validation ou refus)
    
    public function process() 
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=reviews');
            exit();
        }

        $action = $_POST['action'] ?? '';
        $reviewId = $_POST['review_id'] ?? '';

        if (empty($reviewId)) {
            $_SESSION['flash_error'] = "Identifiant d'avis invalide.";
            header('Location: ?page=reviews');
            exit();
        }

        if ($action === 'validate') {
            $updated = $this->avisModel->updateStatut($reviewId, 'valide');
            $_SESSION['flash_success'] = $updated ? "L'avis a été validé et publié." : "Erreur lors de la validation.";
        } elseif ($action === 'reject') {
            $updated = $this->avisModel->updateStatut($reviewId, 'refuse');
            $_SESSION['flash_success'] = $updated ? "L'avis a été refusé." : "Erreur lors du refus.";
        }

        header('Location: ?page=reviews');
        exit();
    }
}