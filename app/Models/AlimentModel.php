<?php

namespace App\Models;

use CodeIgniter\Model;

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