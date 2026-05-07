<?php

namespace App\Models;

use CodeIgniter\Model;
class RegimeModel extends Model
{
    protected $table      = 'regimes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nom_regime', 'duree_jours', 'poids_impact', 'prix_journalier', 'description'];

    /**
     * Retourne les régimes avec leur composition en aliments (pourcentages).
     */
    public function getWithComposition(int $id): ?array
    {
        $db = \Config\Database::connect();
        $regime = $this->find($id);
        if (!$regime) return null;

        $regime['composition'] = $db->query("
            SELECT a.nom_aliment, a.type_aliment, ra.pourcentage
            FROM regime_aliments ra
            JOIN aliments a ON a.id = ra.id_aliment
            WHERE ra.id_regime = ?
        ", [$id])->getResultArray();

        return $regime;
    }

    /**
     * Suggère des régimes selon l'objectif de l'utilisateur :
     *  - 'reduire'  : poids_impact < 0
     *  - 'augmenter': poids_impact > 0
     *  - 'ideal'    : poids_impact = 0 ou proche
     */
    public function getSuggestions(string $objectif): array
    {
        switch ($objectif) {
            case 'reduire':
                return $this->where('poids_impact <', 0)->orderBy('poids_impact', 'ASC')->findAll();
            case 'augmenter':
                return $this->where('poids_impact >', 0)->orderBy('poids_impact', 'DESC')->findAll();
            default: // 'ideal'
                return $this->where('poids_impact', 0)
                            ->orWhere('poids_impact >', -0.5)
                            ->orderBy('ABS(poids_impact)', 'ASC')
                            ->findAll();
        }
    }

    /**
     * Calcule le prix total d'un régime (prix_journalier × duree_jours).
     * Applique une remise de 15% si l'utilisateur est Gold.
     */
    public static function calculerPrix(array $regime, bool $isGold): array
    {
        $prixBrut   = $regime['prix_journalier'] * $regime['duree_jours'];
        $remise     = $isGold ? $prixBrut * 0.15 : 0;
        $prixFinal  = $prixBrut - $remise;

        return [
            'prix_brut'  => $prixBrut,
            'remise'     => $remise,
            'prix_final' => $prixFinal,
            'is_gold'    => $isGold,
        ];
    }
}
