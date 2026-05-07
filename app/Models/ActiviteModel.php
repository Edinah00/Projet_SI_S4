<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modèle pour la table `activites_sportives`.
 */
class ActiviteModel extends Model
{
    protected $table      = 'activites_sportives';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nom_activite', 'poids_impact', 'duree_jours'];

    /**
     * Suggère des activités selon l'objectif :
     *  - 'reduire'  : activités à fort impact négatif
     *  - 'augmenter': activités de prise de masse (impact positif)
     *  - 'ideal'    : activités douces
     */
    public function getSuggestions(string $objectif): array
    {
        switch ($objectif) {
            case 'reduire':
                return $this->where('poids_impact <', 0)->orderBy('poids_impact', 'ASC')->findAll();
            case 'augmenter':
                return $this->where('poids_impact >', 0)->findAll();
            default:
                return $this->findAll();
        }
    }
}
