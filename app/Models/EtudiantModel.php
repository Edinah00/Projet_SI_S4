<?php

namespace App\Models;

use CodeIgniter\Model;

class EtudiantModel extends Model
{
    protected $table = 'etudiants';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'matricule',
        'nom',
        'prenom',
        'parcours',
        'email',
    ];

    protected $validationRules = [
        'matricule' => 'required|is_unique[etudiants.matricule,id,{id}]',
        'nom' => 'required|min_length[2]',
        'prenom' => 'required|min_length[2]',
        'parcours' => 'required|in_list[dev,bddres,web]',
        'email' => 'permit_empty|valid_email',
    ];
}
