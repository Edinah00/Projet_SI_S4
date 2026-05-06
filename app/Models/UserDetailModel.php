<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modèle pour la table `user_details`.
 * Stocke les informations de santé (taille, poids) collectées à l'étape 2.
 */
class UserDetailModel extends Model
{
    protected $table      = 'user_details';
    protected $primaryKey = 'id';

    protected $allowedFields = ['user_id', 'taille', 'poids_actuel'];

    protected $useTimestamps = true;
    protected $createdField  = 'date_enregistrement';
    protected $updatedField  = '';

    /**
     * Retourne le dernier enregistrement de santé pour un utilisateur.
     */
    public function getLatestByUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('date_enregistrement', 'DESC')
                    ->first();
    }

    /**
     * Calcule l'IMC : poids (kg) / taille² (m).
     * Retourne un tableau [imc, categorie, conseils].
     */
    public static function calculerIMC(float $poids, float $taille): array
    {
        if ($taille <= 0) {
            return ['imc' => 0, 'categorie' => 'N/A', 'conseils' => ''];
        }

        $imc = round($poids / ($taille * $taille), 1);

        if ($imc < 18.5) {
            $categorie = 'Insuffisance pondérale';
            $conseil   = 'Vous êtes en sous-poids. Un programme de prise de masse vous est conseillé.';
            $couleur   = '#3b82f6';
        } elseif ($imc < 25.0) {
            $categorie = 'Corpulence normale';
            $conseil   = 'Votre IMC est idéal ! Un programme d\'entretien vous aidera à maintenir cette forme.';
            $couleur   = '#22c55e';
        } elseif ($imc < 30.0) {
            $categorie = 'Surpoids';
            $conseil   = 'Vous êtes en léger surpoids. Un régime alimentaire équilibré est recommandé.';
            $couleur   = '#f59e0b';
        } else {
            $categorie = 'Obésité';
            $conseil   = 'Votre IMC indique une obésité. Consultez un médecin et adoptez un régime adapté.';
            $couleur   = '#ef4444';
        }

        return compact('imc', 'categorie', 'conseil', 'couleur');
    }
}