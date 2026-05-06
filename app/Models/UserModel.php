<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modèle pour la table `users`.
 * Gère l'authentification et les données de profil.
 */
class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nom', 'email', 'password', 'genre', 'role', 'is_gold'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation des données
    protected $validationRules = [
        'nom'      => 'required|min_length[2]|max_length[100]',
        'email'    => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]',
        'genre'    => 'required|in_list[M,F]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Cet email est déjà utilisé.'
        ]
    ];

    /**
     * Retrouve un utilisateur par email (pour la connexion).
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Crée un utilisateur et initialise son porte-monnaie.
     * Retourne l'ID du nouvel utilisateur.
     */
    public function createWithWallet(array $data): int
    {
        $db = \Config\Database::connect();

        $this->insert([
            'nom'      => $data['nom'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'genre'    => $data['genre'],
        ]);

        $userId = $this->insertID();

        // Crée le porte-monnaie à 0
        $db->table('portemonnaie')->insert([
            'user_id' => $userId,
            'solde'   => 0,
        ]);

        return $userId;
    }

    /**
     * Statistiques : nombre d'utilisateurs par objectif
     * (via les programmes achetés).
     */
    public function statsParObjectif(): array
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT
                CASE
                    WHEN r.poids_impact < 0 THEN 'Réduire le poids'
                    WHEN r.poids_impact > 0 THEN 'Augmenter la masse'
                    ELSE 'Maintenir l\'IMC'
                END AS objectif,
                COUNT(DISTINCT up.user_id) AS nb_users
            FROM user_programmes up
            JOIN regimes r ON r.id = up.id_regime
            GROUP BY objectif
        ")->getResultArray();
    }

    /**
     * Revenus totaux générés par les achats de programmes.
     */
    public function revenuTotal(): float
    {
        $db = \Config\Database::connect();
        $row = $db->query("SELECT COALESCE(SUM(prix_total_paye), 0) AS total FROM user_programmes")->getRow();
        return (float)($row->total ?? 0);
    }

    /**
     * Active/désactive le statut Gold d'un utilisateur.
     */
    public function toggleGold(int $userId): void
    {
        $user = $this->find($userId);
        $this->update($userId, ['is_gold' => !$user['is_gold']]);
    }
}