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


/**
 * Modèle pour la table `aliments`.
 */
class AlimentModel extends Model
{
    protected $table      = 'aliments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nom_aliment', 'type_aliment'];

    /** Types disponibles pour les formulaires */
    public static function types(): array
    {
        return ['viande', 'poisson', 'volaille', 'legume'];
    }
}


/**
 * Modèle pour les tables `portemonnaie` et `codes_recharge`.
 */
class PortefeuilleModel extends Model
{
    protected $table      = 'portemonnaie';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'solde'];

    /**
     * Retourne le solde d'un utilisateur.
     */
    public function getSolde(int $userId): float
    {
        $row = $this->where('user_id', $userId)->first();
        return (float)($row['solde'] ?? 0);
    }

    /**
     * Valide un code de recharge et crédite le porte-monnaie.
     * Retourne ['success' => bool, 'message' => string, 'nouveau_solde' => float].
     */
    public function rechargerAvecCode(int $userId, string $code): array
    {
        $db = \Config\Database::connect();

        // Vérifie le code
        $codeRow = $db->table('codes_recharge')
                      ->where('code', $code)
                      ->where('est_valide', 1)
                      ->where('est_utilise', 0)
                      ->get()->getRowArray();

        if (!$codeRow) {
            return ['success' => false, 'message' => 'Code invalide ou déjà utilisé.'];
        }

        // Crédite le porte-monnaie
        $soldeActuel = $this->getSolde($userId);
        $nouveauSolde = $soldeActuel + $codeRow['valeur'];

        $db->table('portemonnaie')->where('user_id', $userId)->update(['solde' => $nouveauSolde]);
        $db->table('codes_recharge')->where('id', $codeRow['id'])->update(['est_utilise' => 1]);

        return [
            'success'       => true,
            'message'       => 'Recharge réussie ! +' . number_format($codeRow['valeur'], 0, ',', ' ') . ' Ar crédités.',
            'nouveau_solde' => $nouveauSolde,
        ];
    }

    /**
     * Débite le porte-monnaie (pour un achat de programme).
     */
    public function debiter(int $userId, float $montant): bool
    {
        $solde = $this->getSolde($userId);
        if ($solde < $montant) return false;

        $db = \Config\Database::connect();
        $db->table('portemonnaie')->where('user_id', $userId)->update(['solde' => $solde - $montant]);
        return true;
    }
}

