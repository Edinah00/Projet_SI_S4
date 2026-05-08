<?php

namespace App\Controllers;

use App\Models\RegimeModel;
use App\Models\ActiviteModel;
use App\Models\PortefeuilleModel;
use App\Models\ProgrammeModel;
use App\Models\UserDetailModel;

/**
 * Gère l'achat de programmes et l'export PDF.
 */
class ProgrammeController extends BaseController
{
    protected function requireLogin()
    {
        if (!$this->session->get('isLoggedIn')) {
            redirect()->to('/login')->send();
            exit;
        }
    }

    /**
     * POST /programme/acheter
     * Débite le porte-monnaie et enregistre le programme.
     */
    public function acheter()
    {
        $this->requireLogin();

        $userId      = $this->session->get('userId');
        $isGold      = (bool)$this->session->get('isGold');
        $idRegime    = (int)$this->request->getPost('id_regime');
        $idActivite  = (int)$this->request->getPost('id_activite');

        $regimeModel = new RegimeModel();
        $regime      = $regimeModel->find($idRegime);

        if (!$regime) {
            return redirect()->to('/suggestions')->with('error', 'Régime introuvable.');
        }

        // Calcul du prix
        $prix = RegimeModel::calculerPrix($regime, $isGold);

        // Récupère le poids actuel pour calculer l'objectif
        $detailModel = new UserDetailModel();
        $detail      = $detailModel->getLatestByUser($userId);
        $poidsVise   = $detail ? round($detail['poids_actuel'] + $regime['poids_impact'], 1) : null;

        // Débite le porte-monnaie
        $walletModel = new PortefeuilleModel();
        $debit = $walletModel->debiter($userId, $prix['prix_final']);
        if (!$debit['success']) {
            return redirect()->to('/suggestions')->with('error', $debit['message']);
        }

        // Enregistre le programme
        $progModel = new ProgrammeModel();
        $progId    = $progModel->insert([
            'user_id'             => $userId,
            'id_regime'           => $idRegime,
            'id_activite'         => $idActivite,
            'poids_objectif_vise' => $poidsVise,
            'prix_total_paye'     => $prix['prix_final'],
        ]);

        return redirect()->to('/programme/pdf/' . $progModel->insertID())
                         ->with('success', 'Programme acheté avec succès !');
    }

    /**
     * GET /programme/pdf/{id}
     * Génère le récapitulatif PDF du programme.
     */
    public function exportPdf(int $id)
    {
        $this->requireLogin();

        $progModel = new ProgrammeModel();
        $prog      = $progModel->getFullById($id);

        if (!$prog || $prog['user_id'] != $this->session->get('userId')) {
            return redirect()->to('/mes-programmes')->with('error', 'Programme introuvable.');
        }

        // Composition du régime
        $db          = \Config\Database::connect();
        $composition = $db->query("
            SELECT a.nom_aliment, a.type_aliment, ra.pourcentage
            FROM regime_aliments ra
            JOIN aliments a ON a.id = ra.id_aliment
            WHERE ra.id_regime = ?
        ", [$prog['id_regime']])->getResultArray();

        $prog['composition'] = $composition;

        // Calcul IMC si disponible
        $imc = null;
        if (!empty($prog['taille']) && !empty($prog['poids_actuel'])) {
            $imc = UserDetailModel::calculerIMC($prog['poids_actuel'], $prog['taille']);
        }

        // ── Génération du PDF avec DomPDF ──────────────────────
        // Si DomPDF n'est pas installé, on génère une page HTML imprimable
        if (class_exists('Dompdf\Dompdf')) {
            $html = view('dashboard/pdf_template', ['prog' => $prog, 'imc' => $imc], ['saveData' => false]);

            $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $dompdf->stream('programme_' . $id . '.pdf', ['Attachment' => true]);
            exit;
        }

        // Fallback : page HTML imprimable
        return view('dashboard/pdf_template', [
            'title' => 'Récapitulatif Programme',
            'prog'  => $prog,
            'imc'   => $imc,
        ]);
    }

    /**
     * GET /mes-programmes — Liste des programmes achetés.
     */
    public function mesProgrammes()
    {
        $this->requireLogin();

        $userId    = $this->session->get('userId');
        $progModel = new ProgrammeModel();

        return view('dashboard/mes_programmes', [
            'title'      => 'Mes Programmes',
            'programmes' => $progModel->getByUser($userId),
        ]);
    }
}
