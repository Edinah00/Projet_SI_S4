<?php

namespace App\Controllers;

use App\Models\EtudiantModel;
use App\Models\MatiereModel;
use App\Models\NoteModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class NoteController extends BaseController
{
    protected EtudiantModel $etudiantModel;
    protected MatiereModel $matiereModel;
    protected NoteModel $noteModel;

    public function __construct()
    {
        $this->etudiantModel = new EtudiantModel();
        $this->matiereModel = new MatiereModel();
        $this->noteModel = new NoteModel();
    }

    public function create()
    {
        return view('note_form', [
            'title' => 'Saisie des notes',
            'etudiants' => $this->etudiantModel->orderBy('nom')->findAll(),
            'matieres' => $this->matiereModel->orderBy('semestre')->orderBy('code')->findAll(),
            'currentRoute' => 'notes',
        ]);
    }

    public function store()
    {
        $data = [
            'etudiant_id' => $this->request->getPost('etudiant_id'),
            'matiere_id' => $this->request->getPost('matiere_id'),
            'note' => $this->request->getPost('note'),
            'date_saisie' => date('Y-m-d'),
        ];

        if (! $this->noteModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->noteModel->errors());
        }

        return redirect()->to('/notes/create')->with('success', 'La note a ete ajoutee avec succes.');
    }

    public function studentNotes(int $etudiantId)
    {
        $etudiant = $this->etudiantModel->find($etudiantId);

        if ($etudiant === null) {
            throw PageNotFoundException::forPageNotFound('Etudiant introuvable.');
        }

        $matieres = $this->matiereModel->orderBy('semestre')->orderBy('code')->findAll();
        $notes = $this->noteModel
            ->select('notes.id, notes.note, notes.date_saisie, notes.matiere_id, matieres.code, matieres.intitule, matieres.semestre')
            ->join('matieres', 'matieres.id = notes.matiere_id')
            ->where('notes.etudiant_id', $etudiantId)
            ->orderBy('matieres.semestre', 'ASC')
            ->orderBy('matieres.code', 'ASC')
            ->orderBy('notes.date_saisie', 'DESC')
            ->findAll();

        return view('student_notes_manage', [
            'title' => 'Gestion des notes',
            'etudiant' => $etudiant,
            'matieres' => $matieres,
            'notes' => $notes,
            'currentRoute' => 'etudiants',
        ]);
    }

    public function storeForStudent(int $etudiantId)
    {
        $etudiant = $this->etudiantModel->find($etudiantId);

        if ($etudiant === null) {
            throw PageNotFoundException::forPageNotFound('Etudiant introuvable.');
        }

        $data = [
            'etudiant_id' => $etudiantId,
            'matiere_id' => $this->request->getPost('matiere_id'),
            'note' => $this->request->getPost('note'),
            'date_saisie' => $this->request->getPost('date_saisie') ?: date('Y-m-d'),
        ];

        if (! $this->noteModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->noteModel->errors());
        }

        return redirect()->to('/etudiants/' . $etudiantId . '/notes')->with('success', 'La note a ete ajoutee.');
    }

    public function updateForStudent(int $etudiantId, int $noteId)
    {
        $note = $this->noteModel->find($noteId);

        if ($note === null || (int) $note['etudiant_id'] !== $etudiantId) {
            throw PageNotFoundException::forPageNotFound('Note introuvable.');
        }

        $data = [
            'matiere_id' => $this->request->getPost('matiere_id'),
            'note' => $this->request->getPost('note'),
            'date_saisie' => $this->request->getPost('date_saisie') ?: $note['date_saisie'],
        ];

        if (! $this->noteModel->update($noteId, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->noteModel->errors());
        }

        return redirect()->to('/etudiants/' . $etudiantId . '/notes')->with('success', 'La note a ete modifiee.');
    }

    public function deleteForStudent(int $etudiantId, int $noteId)
    {
        $note = $this->noteModel->find($noteId);

        if ($note === null || (int) $note['etudiant_id'] !== $etudiantId) {
            throw PageNotFoundException::forPageNotFound('Note introuvable.');
        }

        $this->noteModel->delete($noteId);

        return redirect()->to('/etudiants/' . $etudiantId . '/notes')->with('success', 'La note a ete supprimee.');
    }
}
