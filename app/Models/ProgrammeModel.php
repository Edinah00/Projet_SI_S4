<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modèle pour la table `user_programmes`.
 */
class ProgrammeModel extends Model
{
    protected $table      = 'user_programmes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'id_regime', 'id_activite',
        'poids_objectif_vise', 'prix_total_paye'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'date_achat';
    protected $updatedField  = '';

    /**
     * Retourne les programmes achetés par un utilisateur,
     * avec les détails du régime et de l'activité.
     */
    public function getByUser(int $userId): array
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT up.*, r.nom_regime, r.duree_jours, r.poids_impact,
                   a.nom_activite, up.date_achat, up.prix_total_paye
            FROM user_programmes up
            JOIN regimes r ON r.id = up.id_regime
            JOIN activites_sportives a ON a.id = up.id_activite
            WHERE up.user_id = ?
            ORDER BY up.date_achat DESC
        ", [$userId])->getResultArray();
    }

    /**
     * Retourne un programme complet pour l'export PDF.
     */
    public function getFullById(int $id): ?array
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT up.*,
                   u.nom AS user_nom, u.email AS user_email, u.genre, u.is_gold,
                   ud.taille, ud.poids_actuel,
                   r.nom_regime, r.duree_jours, r.prix_journalier, r.description AS regime_desc,
                   a.nom_activite, a.duree_jours AS activite_duree
            FROM user_programmes up
            JOIN users u        ON u.id = up.user_id
            LEFT JOIN user_details ud ON ud.user_id = up.user_id
            JOIN regimes r      ON r.id = up.id_regime
            JOIN activites_sportives a ON a.id = up.id_activite
            WHERE up.id = ?
        ", [$id])->getRowArray();
    }
}
