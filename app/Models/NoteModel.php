<?php

namespace App\Models;

use CodeIgniter\Model;

class NoteModel extends Model
{
    protected $table = 'notes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'etudiant_id',
        'matiere_id',
        'note',
        'date_saisie',
    ];

    protected $validationRules = [
        'etudiant_id' => 'required|integer',
        'matiere_id' => 'required|integer',
        'note' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[20]',
    ];
}
