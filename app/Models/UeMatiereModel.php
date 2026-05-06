<?php

namespace App\Models;

use CodeIgniter\Model;

class UeMatiereModel extends Model
{
    protected $table = 'ue_matieres';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'ue_id',
        'matiere_id',
    ];
}
