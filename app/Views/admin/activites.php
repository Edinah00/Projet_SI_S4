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
            <span class="topbar-title">🏃 Gestion des Activités Sportives</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-create')">+ Ajouter une activité</button>
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
                                <th>Activité</th>
                                <th>Impact poids/jour</th>
                                <th>Durée programme</th>
                                <th>Effet total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($activites)): ?>
                            <tr><td colspan="6" class="text-center" style="padding:32px;color:var(--ink-muted)">Aucune activité enregistrée</td></tr>
                        <?php endif; ?>
                        <?php foreach ($activites as $a): ?>
                            <tr>
                                <td style="color:var(--ink-muted);font-size:.8rem"><?= $a['id'] ?></td>
                                <td><strong><?= esc($a['nom_activite']) ?></strong></td>
                                <td>
                                    <span style="font-weight:600;color:<?= $a['poids_impact'] < 0 ? 'var(--red)' : 'var(--green-500)' ?>">
                                        <?= $a['poids_impact'] > 0 ? '+' : '' ?><?= $a['poids_impact'] ?> kg/j
                                    </span>
                                </td>
                                <td><?= $a['duree_jours'] ?> jours</td>
                                <td>
                                    <?php $total = round($a['poids_impact'] * $a['duree_jours'], 1); ?>
                                    <span style="font-weight:600;color:<?= $total < 0 ? 'var(--red)' : 'var(--green-500)' ?>">
                                        <?= $total > 0 ? '+' : '' ?><?= $total ?> kg
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-8">
                                        <button class="btn btn-secondary btn-sm"
                                                onclick="openEdit(<?= $a['id'] ?>, '<?= esc(addslashes($a['nom_activite'])) ?>', <?= $a['poids_impact'] ?>, <?= $a['duree_jours'] ?>)">
                                            ✏️ Modifier
                                        </button>
                                        <form action="<?= site_url('/admin/activites/delete/' . $a['id']) ?>" method="post"
                                              onsubmit="return confirm('Supprimer cette activité ?')">
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

<!-- Modal Créer -->
<div class="modal-backdrop" id="modal-create" onclick="closeOnBackdrop(event,'modal-create')">
    <div class="modal">
        <div class="modal-header">
            <h3 style="font-size:1.1rem">Ajouter une activité</h3>
            <button class="modal-close" onclick="closeModal('modal-create')">✕</button>
        </div>
        <form action="<?= site_url('/admin/activites/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nom de l'activité *</label>
                    <input class="form-control" name="nom_activite" placeholder="Ex: Natation" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Impact poids/jour (kg) *</label>
                        <input class="form-control" type="number" name="poids_impact" step="0.01" placeholder="-0.05 ou +0.08" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Durée (jours) *</label>
                        <input class="form-control" type="number" name="duree_jours" placeholder="30" min="1" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-create')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Modifier -->
<div class="modal-backdrop" id="modal-edit" onclick="closeOnBackdrop(event,'modal-edit')">
    <div class="modal">
        <div class="modal-header">
            <h3 style="font-size:1.1rem">Modifier l'activité</h3>
            <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
        </div>
        <form id="form-edit" method="post">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nom de l'activité *</label>
                    <input class="form-control" name="nom_activite" id="edit-nom" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Impact poids/jour (kg) *</label>
                        <input class="form-control" type="number" name="poids_impact" id="edit-impact" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Durée (jours) *</label>
                        <input class="form-control" type="number" name="duree_jours" id="edit-duree" min="1" required>
                    </div>
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
function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
function closeOnBackdrop(e,id) { if(e.target===e.currentTarget) closeModal(id); }
function openEdit(id, nom, impact, duree) {
    document.getElementById('form-edit').action = '<?= site_url("/admin/activites/update/") ?>' + id;
    document.getElementById('edit-nom').value    = nom;
    document.getElementById('edit-impact').value = impact;
    document.getElementById('edit-duree').value  = duree;
    openModal('modal-edit');
}
</script>
</body>
</html>
