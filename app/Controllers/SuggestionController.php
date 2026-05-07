<?php

namespace App\Controllers;

use App\Models\RegimeModel;
use App\Models\ActiviteModel;

/**
 * Moteur de suggestion de régimes et activités selon l'objectif.
 */
class SuggestionController extends BaseController
{
    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        return view('dashboard/suggestions', ['title' => 'Trouver mon programme']);
    }

    /**
     * AJAX POST /suggestions/get
     * Paramètre : objectif = 'reduire' | 'augmenter' | 'ideal'
     * Retourne JSON { regimes[], activites[], isGold, prixDetails{} }.
     */
    public function getSuggestions()
    {
        if (!$this->request->isAJAX() || !$this->session->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Non autorisé']);
        }

        $objectif = $this->request->getPost('objectif');
        $isGold   = (bool)$this->session->get('isGold');

        $regimeModel  = new RegimeModel();
        $activiteModel= new ActiviteModel();

        $regimes  = $regimeModel->getSuggestions($objectif);
        $activites= $activiteModel->getSuggestions($objectif);

        // Calcul des prix pour chaque régime (avec remise Gold éventuelle)
        $prixDetails = [];
        foreach ($regimes as &$regime) {
            $prix = RegimeModel::calculerPrix($regime, $isGold);
            $regime['prix_brut']  = $prix['prix_brut'];
            $regime['remise']     = $prix['remise'];
            $regime['prix_final'] = $prix['prix_final'];
        }

        return $this->response->setJSON([
            'success'  => true,
            'regimes'  => $regimes,
            'activites'=> $activites,
            'isGold'   => $isGold,
            'objectif' => $objectif,
        ]);
    }
}
