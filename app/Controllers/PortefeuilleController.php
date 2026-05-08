<?php

namespace App\Controllers;

use App\Models\PortefeuilleModel;
use App\Models\UserModel;

/**
 * Gestion du porte-monnaie utilisateur.
 */
class PortefeuilleController extends BaseController
{
    protected function requireLogin()
    {
        if (!$this->session->get('isLoggedIn')) {
            redirect()->to('/login')->send();
            exit;
        }
    }

    public function index()
    {
        $this->requireLogin();
        $userId      = $this->session->get('userId');
        $walletModel = new PortefeuilleModel();

        return view('dashboard/portefeuille', [
            'title' => 'Mon Porte-monnaie',
            'solde' => $walletModel->getSolde($userId),
        ]);
    }

    /**
     * AJAX POST /portefeuille/recharger
     * Retourne JSON { success, message, nouveau_solde }.
     */
    public function recharger()
    {
        if (!$this->request->isAJAX() || !$this->session->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Non autorisé',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $code        = trim($this->request->getPost('code'));
        $userId      = $this->session->get('userId');
        $walletModel = new PortefeuilleModel();

        $result = $walletModel->rechargerAvecCode($userId, $code);
        $result['csrfHash'] = csrf_hash();
        return $this->response->setJSON($result);
    }

    /**
     * AJAX POST /portefeuille/demander-gold
     * Permet à l'utilisateur de signaler sa demande Gold.
     */
    public function demanderGold()
    {
        if (!$this->request->isAJAX() || !$this->session->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Non autorisé']);
        }

        $userId = (int)$this->session->get('userId');
        (new UserModel())->requestGold($userId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Votre demande Gold a bien été envoyée à l\'administrateur.',
            'csrfHash' => csrf_hash(),
        ]);
    }
}
