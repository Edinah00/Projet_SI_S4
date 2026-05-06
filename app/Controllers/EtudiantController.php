<?php

namespace App\Controllers;

use App\Models\EtudiantModel;
use App\Models\NoteModel;
use App\Models\UeModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class EtudiantController extends BaseController
{
    protected EtudiantModel $etudiantModel;
    protected NoteModel $noteModel;
    protected UeModel $ueModel;

    public function __construct()
    {
        $this->etudiantModel = new EtudiantModel();
        $this->noteModel = new NoteModel();
        $this->ueModel = new UeModel();
    }

    public function index()
    {
        $etudiants = $this->etudiantModel->orderBy('nom')->findAll();

        foreach ($etudiants as &$etudiant) {
            $semestre3 = $this->buildReleve($etudiant, 3);
            $semestre4 = $this->buildReleve($etudiant, 4);
            $global = $this->buildGlobalReleve($etudiant);

            $etudiant['moyenne_s3'] = $semestre3['moyenne'];
            $etudiant['moyenne_s4'] = $semestre4['moyenne'];
            $etudiant['moyenne_l2'] = $global['moyenne'];
            $etudiant['mention'] = $global['mention'];
        }
        unset($etudiant);

        return view('students_list', [
            'title' => 'Liste des etudiants',
            'etudiants' => $etudiants,
            'currentRoute' => 'etudiants',
        ]);
    }

    public function show(int $id)
    {
        $etudiant = $this->etudiantModel->find($id);

        if ($etudiant === null) {
            throw PageNotFoundException::forPageNotFound('Etudiant introuvable.');
        }

        $vue = $this->request->getGet('vue') ?: 's3';
        $vue = in_array($vue, ['s3', 's4', 'l2'], true) ? $vue : 's3';

        $notes = $this->noteModel
            ->select('notes.id, notes.note, notes.date_saisie, matieres.code, matieres.intitule, matieres.semestre')
            ->join('matieres', 'matieres.id = notes.matiere_id')
            ->where('notes.etudiant_id', $id)
            ->orderBy('matieres.semestre', 'ASC')
            ->orderBy('matieres.code', 'ASC')
            ->orderBy('notes.note', 'DESC')
            ->findAll();

        return view('student_show', [
            'title' => 'Releve de notes',
            'etudiant' => $etudiant,
            'notes' => $notes,
            'selectedView' => $vue,
            'semestre3' => $this->buildReleve($etudiant, 3),
            'semestre4' => $this->buildReleve($etudiant, 4),
            'global' => $this->buildGlobalReleve($etudiant),
            'currentRoute' => 'etudiants',
        ]);
    }

    protected function buildGlobalReleve(array $etudiant): array
    {
        $semestre3 = $this->buildReleve($etudiant, 3);
        $semestre4 = $this->buildReleve($etudiant, 4);

        $totalCredits = $semestre3['credits'] + $semestre4['credits'];
        $totalMaxCredits = $semestre3['max_credits'] + $semestre4['max_credits'];
        $weightedTotal = ($semestre3['moyenne'] * $semestre3['max_credits']) + ($semestre4['moyenne'] * $semestre4['max_credits']);
        $moyenne = $totalMaxCredits > 0 ? round($weightedTotal / $totalMaxCredits, 2) : 0.0;

        return [
            'rows' => array_merge($semestre3['rows'], $semestre4['rows']),
            'credits' => $totalCredits,
            'max_credits' => $totalMaxCredits,
            'moyenne' => $moyenne,
            'mention' => $this->getMentionLabel($moyenne),
            'resultat' => $this->getDecisionLabel($moyenne),
        ];
    }

    protected function buildReleve(array $etudiant, int $semestre): array
    {
        $programme = $this->getProgramme($etudiant['parcours'], $semestre);
        $rows = [];
        $somme = 0.0;
        $credits = 0;
        $maxCredits = 0;

        foreach ($programme as $item) {
            $maxCredits += (int) $item['credits'];

            if ($item['type_ue'] === 'single') {
                $matiere = $item['matieres'][0] ?? null;
                if ($matiere === null) {
                    continue;
                }

                $note = $this->getBestNote($etudiant['id'], (int) $matiere['id']);
                $rows[] = $this->formatRow($matiere, $note, $item['libelle'], (int) $item['credits']);
                $somme += $note * (int) $item['credits'];
                $credits += $note >= 10 ? (int) $item['credits'] : 0;
                continue;
            }

            $bestOption = null;
            foreach ($item['matieres'] as $matiere) {
                $note = $this->getBestNote($etudiant['id'], (int) $matiere['id']);
                if ($bestOption === null || $note > $bestOption['note']) {
                    $bestOption = [
                        'matiere' => $matiere,
                        'note' => $note,
                    ];
                }
            }

            if ($bestOption === null) {
                continue;
            }

            $rows[] = $this->formatRow($bestOption['matiere'], $bestOption['note'], $item['libelle'], (int) $item['credits']);
            $somme += $bestOption['note'] * (int) $item['credits'];
            $credits += $bestOption['note'] >= 10 ? (int) $item['credits'] : 0;
        }

        $moyenne = $maxCredits > 0 ? round($somme / $maxCredits, 2) : 0.0;

        return [
            'rows' => $rows,
            'credits' => $credits,
            'max_credits' => $maxCredits,
            'moyenne' => $moyenne,
            'mention' => $this->getMentionLabel($moyenne),
            'resultat' => $this->getDecisionLabel($moyenne),
        ];
    }

    protected function getBestNote(int $etudiantId, int $matiereId): float
    {
        $note = $this->noteModel
            ->where('etudiant_id', $etudiantId)
            ->where('matiere_id', $matiereId)
            ->selectMax('note')
            ->first();

        return isset($note['note']) ? (float) $note['note'] : 0.0;
    }

    protected function formatRow(array $matiere, float $note, ?string $label, int $creditsMax): array
    {
        return [
            'code' => $matiere['code'],
            'intitule' => $matiere['intitule'],
            'credits' => $note >= 10 ? $creditsMax : 0,
            'credits_max' => $creditsMax,
            'note' => $note,
            'resultat' => $this->getSubjectDecisionLabel($note),
            'label' => $label,
        ];
    }

    protected function getProgramme(string $parcours, int $semestre): array
    {
        $rows = $this->fetchProgrammeRows($parcours, $semestre);
        $programme = [];

        foreach ($rows as $row) {
            $ueId = (int) $row['id'];

            if (! isset($programme[$ueId])) {
                $programme[$ueId] = [
                    'id' => $ueId,
                    'libelle' => $row['type_ue'] === 'option' ? $row['libelle'] : null,
                    'semestre' => (int) $row['semestre'],
                    'parcours' => $row['parcours'],
                    'type_ue' => $row['type_ue'],
                    'credits' => (int) $row['credits'],
                    'matieres' => [],
                ];
            }

            $programme[$ueId]['matieres'][] = [
                'id' => (int) $row['matiere_id'],
                'code' => $row['code'],
                'intitule' => $row['intitule'],
                'semestre' => (int) $row['matiere_semestre'],
            ];
        }

        return array_values($programme);
    }

    protected function fetchProgrammeRows(string $parcours, int $semestre): array
    {
        return $this->ueModel
            ->select('ues.id, ues.libelle, ues.semestre, ues.parcours, ues.type_ue, ues.credits, matieres.id as matiere_id, matieres.code, matieres.intitule, matieres.semestre as matiere_semestre')
            ->join('ue_matieres', 'ue_matieres.ue_id = ues.id')
            ->join('matieres', 'matieres.id = ue_matieres.matiere_id')
            ->where('ues.semestre', $semestre)
            ->groupStart()
                ->where('ues.parcours', 'tronc_commun')
                ->orWhere('ues.parcours', $parcours)
            ->groupEnd()
            ->orderBy('ues.id', 'ASC')
            ->orderBy('matieres.code', 'ASC')
            ->findAll();
    }

    protected function getSubjectDecisionLabel(float $note): string
    {
        if ($note < 10) {
            return 'Comp.';
        }

        if ($note < 12) {
            return 'P';
        }

        if ($note < 14) {
            return 'AB';
        }

        if ($note < 16) {
            return 'B';
        }

        return 'TB';
    }

    protected function getMentionLabel(float $note): string
    {
        if ($note < 10) {
            return 'Ajourne';
        }

        if ($note < 12) {
            return 'Passable';
        }

        if ($note < 14) {
            return 'Assez bien';
        }

        if ($note < 16) {
            return 'Bien';
        }

        return 'Tres bien';
    }

    protected function getDecisionLabel(float $note): string
    {
        return $note >= 10 ? 'Admis(e)' : 'Ajourne';
    }
}
