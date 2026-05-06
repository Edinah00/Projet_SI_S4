<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - TP Releve</title>
    <link rel="stylesheet" href="<?= base_url('style.css') ?>">
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="logo-icon"><svg viewBox="0 0 24 24" width="18" height="18"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></div>
            <div><div class="brand-name">TP Releve</div><div class="brand-sub">v1.0.0</div></div>
        </div>
        <div class="sidebar-section">Navigation</div>
        <a href="<?= site_url('/etudiants') ?>" class="nav-item active">Liste des etudiants</a>
        <a href="<?= site_url('/notes/create') ?>" class="nav-item">Saisie des notes</a>
        <div class="sidebar-bottom">
            <a href="<?= site_url('/logout') ?>" class="user-row"><div class="avatar">AD</div><div class="user-info"><div class="name">Admin TP</div><div class="role">Deconnexion</div></div></a>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="topbar-title">Gestion des notes</div>
            <div class="topbar-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" placeholder="Rechercher..." /></div>
        </div>

        <div class="content">
            <div class="page-header">
                <div>
                    <h2><?= esc($etudiant['nom'] . ' ' . $etudiant['prenom']) ?></h2>
                    <div class="breadcrumb">CRUD / <span><?= esc($etudiant['matricule']) ?></span></div>
                </div>
                <a class="btn btn-secondary" href="<?= site_url('/etudiants/' . $etudiant['id']) ?>">Retour au releve</a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert error"><?php foreach (session()->getFlashdata('errors') as $error): ?><p><?= esc($error) ?></p><?php endforeach; ?></div>
            <?php endif; ?>

            <div class="form-card" style="margin-bottom:24px">
                <div class="form-section-title">Ajouter une note</div>
                <form action="<?= site_url('/etudiants/' . $etudiant['id'] . '/notes/store') ?>" method="post" class="form-grid cols-3" id="student-note-create-form">
                    <?= csrf_field() ?>
                    <div>
                        <label class="field-label" for="matiere_id">Matiere</label>
                        <select id="matiere_id" name="matiere_id" required>
                            <option value="">Choisir une matiere</option>
                            <?php foreach ($matieres as $matiere): ?>
                                <option value="<?= $matiere['id'] ?>" <?= old('matiere_id') == $matiere['id'] ? 'selected' : '' ?>>S<?= esc($matiere['semestre']) ?> - <?= esc($matiere['code']) ?> - <?= esc($matiere['intitule']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="field-label" for="note">Note</label>
                        <input id="note" name="note" type="number" min="0" max="20" step="0.25" value="<?= esc(old('note', '10')) ?>" required>
                    </div>
                    <div>
                        <label class="field-label" for="date_saisie">Date</label>
                        <input id="date_saisie" name="date_saisie" type="date" value="<?= esc(old('date_saisie', date('Y-m-d'))) ?>" required>
                    </div>
                </form>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary" form="student-note-create-form">Ajouter</button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Toutes les notes de l'etudiant</div>
                    <div class="panel-note">Modification et suppression directes dans le tableau.</div>
                </div>

                <div class="table-card" style="box-shadow:none">
                    <table class="crud-table">
                        <thead>
                            <tr>
                                <th>Semestre</th>
                                <th>Matiere</th>
                                <th>Note</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notes as $note): ?>
                                <form id="note-form-<?= $note['id'] ?>" action="<?= site_url('/etudiants/' . $etudiant['id'] . '/notes/update/' . $note['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                </form>
                                <tr>
                                    <td>S<?= esc($note['semestre']) ?></td>
                                    <td>
                                        <select name="matiere_id" form="note-form-<?= $note['id'] ?>" required>
                                            <?php foreach ($matieres as $matiere): ?>
                                                <option value="<?= $matiere['id'] ?>" <?= (int) $note['matiere_id'] === (int) $matiere['id'] ? 'selected' : '' ?>>S<?= esc($matiere['semestre']) ?> - <?= esc($matiere['code']) ?> - <?= esc($matiere['intitule']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input name="note" type="number" min="0" max="20" step="0.25" value="<?= esc($note['note']) ?>" form="note-form-<?= $note['id'] ?>" required></td>
                                    <td><input name="date_saisie" type="date" value="<?= esc($note['date_saisie']) ?>" form="note-form-<?= $note['id'] ?>" required></td>
                                    <td>
                                        <div class="form-actions-inline">
                                            <button type="submit" class="btn btn-secondary btn-sm" form="note-form-<?= $note['id'] ?>">Modifier</button>
                                            <button type="submit" class="btn btn-danger btn-sm" form="note-form-<?= $note['id'] ?>" formaction="<?= site_url('/etudiants/' . $etudiant['id'] . '/notes/delete/' . $note['id']) ?>" onclick="return confirm('Supprimer cette note ?');">Supprimer</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
