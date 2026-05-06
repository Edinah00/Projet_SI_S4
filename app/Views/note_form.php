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
        <a href="<?= site_url('/etudiants') ?>" class="nav-item">Liste des etudiants</a>
        <a href="<?= site_url('/notes/create') ?>" class="nav-item active">Saisie des notes</a>
        <div class="sidebar-bottom">
            <a href="<?= site_url('/logout') ?>" class="user-row"><div class="avatar">AD</div><div class="user-info"><div class="name">Admin TP</div><div class="role">Deconnexion</div></div></a>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="topbar-title">Saisie des notes</div>
            <div class="topbar-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" placeholder="Rechercher..." /></div>
        </div>

        <div class="content">
            <div class="page-header">
                <div>
                    <h2>Saisir une note</h2>
                    <div class="breadcrumb">Accueil / <span>Notes</span></div>
                </div>
                <a href="<?= site_url('/etudiants') ?>" class="btn btn-secondary">Retour a la liste</a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert error"><?php foreach (session()->getFlashdata('errors') as $error): ?><p><?= esc($error) ?></p><?php endforeach; ?></div>
            <?php endif; ?>

            <div class="form-card">
                <div class="form-section-title">Nouvelle note</div>
                <form action="<?= site_url('/notes/store') ?>" method="post" class="form-grid cols-3">
                    <?= csrf_field() ?>
                    <div>
                        <label class="field-label" for="etudiant_id">Etudiant</label>
                        <select id="etudiant_id" name="etudiant_id" required>
                            <option value="">Choisir un etudiant</option>
                            <?php foreach ($etudiants as $etudiant): ?>
                                <option value="<?= $etudiant['id'] ?>" <?= old('etudiant_id') == $etudiant['id'] ? 'selected' : '' ?>><?= esc($etudiant['matricule'] . ' - ' . $etudiant['nom'] . ' ' . $etudiant['prenom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
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
                        <label class="field-label" for="note">Note /20</label>
                        <input id="note" name="note" type="number" min="0" max="20" step="0.25" value="<?= esc(old('note', '10')) ?>" required>
                    </div>
                </form>
                <div class="form-footer">
                    <button type="submit" formmethod="post" formaction="<?= site_url('/notes/store') ?>" form="note-create-form" style="display:none"></button>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function () {
              const form = document.querySelector('.form-card form');
              form.id = 'note-create-form';
              const footer = document.querySelector('.form-footer');
              const btn = document.createElement('button');
              btn.type = 'submit';
              btn.className = 'btn btn-primary';
              btn.textContent = 'Enregistrer';
              btn.setAttribute('form', 'note-create-form');
              footer.appendChild(btn);
            });
            </script>
        </div>
    </div>
</div>
</body>
</html>
