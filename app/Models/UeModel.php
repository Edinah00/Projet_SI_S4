<?php

namespace App\Models;

use CodeIgniter\Model;

class UeModel extends Model
{
    protected $table = 'ues';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'libelle',
        'semestre',
        'parcours',
        'type_ue',
        'credits',
    ];
}
