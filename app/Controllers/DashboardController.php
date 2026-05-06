<?php

namespace App\Controllers;

use App\Models\UserDetailModel;
use App\Models\ProgrammeModel;

/**
 * Dashboard utilisateur : IMC + résumé.
 */
class DashboardController extends BaseController
{
    public function index()
    {
        $this->requireLogin();

        $userId      = $this->session->get('userId');
        $detailModel = new UserDetailModel();
        $detail      = $detailModel->getLatestByUser($userId);

        $imc = null;
        if ($detail) {
            $imc = UserDetailModel::calculerIMC($detail['poids_actuel'], $detail['taille']);
        }

        $progModel    = new ProgrammeModel();
        $programmes   = $progModel->getByUser($userId);

        // Solde porte-monnaie
        $db    = \Config\Database::connect();
        $wallet= $db->table('portemonnaie')->where('user_id', $userId)->get()->getRowArray();

        return view('dashboard/index', [
            'title'      => 'Mon Dashboard',
            'detail'     => $detail,
            'imc'        => $imc,
            'programmes' => $programmes,
            'solde'      => $wallet['solde'] ?? 0,
        ]);
    }

    /** Middleware simple : redirige si non connecté. */
    protected function requireLogin()
    {
        if (!$this->session->get('isLoggedIn')) {
            redirect()->to('/login')->send();
            exit;
        }
    }
}