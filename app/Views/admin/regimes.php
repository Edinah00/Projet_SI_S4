<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> — Admin</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>
<div class="app-wrapper">
    <?= view('partials/sidebar') ?>

    <div class="main-content">
        <div class="topbar">
            <span class="topbar-title">🥗 Gestion des Régimes</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-create')">+ Ajouter un régime</button>
        </div>

        <div class="page-body">
            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom du régime</th>
                                <th>Impact poids</th>
                                <th>Durée</th>
                                <th>Prix/jour</th>
                                <th>Prix total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($regimes)): ?>
                            <tr><td colspan="7" class="text-center" style="padding:32px;color:var(--ink-muted)">Aucun régime enregistré</td></tr>
                        <?php endif; ?>
                        <?php foreach ($regimes as $r): ?>
                            <tr>
                                <td style="color:var(--ink-muted);font-size:.8rem"><?= $r['id'] ?></td>
                                <td>
                                    <strong><?= esc($r['nom_regime']) ?></strong>
                                    <?php if ($r['description']): ?>
                                    <div style="font-size:.75rem;color:var(--ink-muted);margin-top:2px"><?= esc(substr($r['description'], 0, 60)) ?>...</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-weight:600;color:<?= $r['poids_impact'] < 0 ? 'var(--red)' : 'var(--green-500)' ?>">
                                        <?= $r['poids_impact'] > 0 ? '+' : '' ?><?= $r['poids_impact'] ?> kg
                                    </span>
                                </td>
                                <td><?= $r['duree_jours'] ?> jours</td>
                                <td><?= number_format($r['prix_journalier'], 0, ',', ' ') ?> Ar</td>
                                <td><strong><?= number_format($r['prix_journalier'] * $r['duree_jours'], 0, ',', ' ') ?> Ar</strong></td>
                                <td>
                                    <div class="d-flex gap-8">
                                        <button class="btn btn-secondary btn-sm"
                                                onclick="openEdit(<?= $r['id'] ?>, '<?= esc(addslashes($r['nom_regime'])) ?>', <?= $r['poids_impact'] ?>, <?= $r['duree_jours'] ?>, <?= $r['prix_journalier'] ?>, '<?= esc(addslashes($r['description'] ?? '')) ?>')">
                                            ✏️ Modifier
                                        </button>
                                        <form action="<?= site_url('/admin/regimes/delete/' . $r['id']) ?>" method="post"
                                              onsubmit="return confirm('Supprimer ce régime ?')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-danger btn-sm">🗑️</button>
                                        </form>
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

<!-- ── Modal Créer ── -->
<div class="modal-backdrop" id="modal-create" onclick="closeOnBackdrop(event, 'modal-create')">
    <div class="modal">
        <div class="modal-header">
            <h3 style="font-size:1.1rem">Ajouter un régime</h3>
            <button class="modal-close" onclick="closeModal('modal-create')">✕</button>
        </div>
        <form action="<?= site_url('/admin/regimes/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nom du régime *</label>
                    <input class="form-control" name="nom_regime" placeholder="Ex: Régime Méditerranéen" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Impact poids (kg) *</label>
                        <input class="form-control" type="number" name="poids_impact" step="0.5" placeholder="-2.0 ou +1.5" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Durée (jours) *</label>
                        <input class="form-control" type="number" name="duree_jours" placeholder="30" min="1" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Prix journalier (Ar) *</label>
                    <input class="form-control" type="number" name="prix_journalier" placeholder="7000" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Description du régime..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-create')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- ── Modal Modifier ── -->
<div class="modal-backdrop" id="modal-edit" onclick="closeOnBackdrop(event, 'modal-edit')">
    <div class="modal">
        <div class="modal-header">
            <h3 style="font-size:1.1rem">Modifier le régime</h3>
            <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
        </div>
        <form id="form-edit" method="post">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nom du régime *</label>
                    <input class="form-control" name="nom_regime" id="edit-nom" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Impact poids (kg) *</label>
                        <input class="form-control" type="number" name="poids_impact" id="edit-impact" step="0.5" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Durée (jours) *</label>
                        <input class="form-control" type="number" name="duree_jours" id="edit-duree" min="1" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Prix journalier (Ar) *</label>
                    <input class="form-control" type="number" name="prix_journalier" id="edit-prix" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="edit-desc" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-edit')">Annuler</button>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}
function closeOnBackdrop(e, id) {
    if (e.target === e.currentTarget) closeModal(id);
}
function openEdit(id, nom, impact, duree, prix, desc) {
    document.getElementById('form-edit').action = '<?= site_url("/admin/regimes/update/") ?>' + id;
    document.getElementById('edit-nom').value    = nom;
    document.getElementById('edit-impact').value = impact;
    document.getElementById('edit-duree').value  = duree;
    document.getElementById('edit-prix').value   = prix;
    document.getElementById('edit-desc').value   = desc;
    openModal('modal-edit');
}
</script>
</body>
</html>
