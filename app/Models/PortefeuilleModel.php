<?php

namespace App\Models;

use CodeIgniter\Model;


/**
 * Modèle pour les tables `portemonnaie` et `codes_recharge`.
 */
class PortefeuilleModel extends Model
{
    protected $table      = 'portemonnaie';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'solde'];

    /**
     * Retourne la ligne portefeuille la plus récente pour un utilisateur.
     * Si des doublons existent déjà, on prend la dernière entrée.
     */
    private function getWalletRow(int $userId): ?array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('id', 'DESC')
                    ->first();
    }

    /**
     * S'assure qu'un portefeuille existe pour l'utilisateur.
     */
    private function ensureWalletExists(int $userId): void
    {
        if (!$this->where('user_id', $userId)->first()) {
            $this->insert([
                'user_id' => $userId,
                'solde'   => 0,
            ]);
        }
    }

    /**
     * Retourne le solde d'un utilisateur.
     */
    public function getSolde(int $userId): float
    {
        $row = $this->getWalletRow($userId);
        return (float)($row['solde'] ?? 0);
    }

    /**
     * Crédite le porte-monnaie d'un utilisateur.
     * Retourne le nouveau solde.
     */
    public function crediter(int $userId, float $montant): float
    {
        if ($montant <= 0) {
            return $this->getSolde($userId);
        }

        $db = \Config\Database::connect();
        $this->ensureWalletExists($userId);
        $row = $this->getWalletRow($userId);

        $soldeActuel  = (float)($row['solde'] ?? 0);
        $nouveauSolde = $soldeActuel + $montant;

        if ($row) {
            $db->table('portemonnaie')
               ->where('id', $row['id'])
               ->update(['solde' => $nouveauSolde]);
        }

        return $nouveauSolde;
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

        // Crédite le porte-monnaie en conservant le solde actuel
        $nouveauSolde = $this->crediter($userId, (float)$codeRow['valeur']);
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
        $this->ensureWalletExists($userId);
        $row = $this->getWalletRow($userId);
        if ($row) {
            $db->table('portemonnaie')
               ->where('id', $row['id'])
               ->update(['solde' => $solde - $montant]);
        }
        return true;
    }
}
