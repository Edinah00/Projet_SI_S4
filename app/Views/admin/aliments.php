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
            <span class="topbar-title">🍽️ Gestion des Aliments</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-create')">+ Ajouter un aliment</button>
        </div>

        <div class="page-body">
            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <!-- Filtres par type -->
            <div class="d-flex gap-8 mb-16 flex-wrap">
                <button class="btn btn-secondary btn-sm filter-btn active" onclick="filterType('all', this)">Tous</button>
                <?php foreach ($types as $type): ?>
                <button class="btn btn-outline btn-sm filter-btn" onclick="filterType('<?= $type ?>', this)" style="text-transform:capitalize">
                    <?= $type ?>
                </button>
                <?php endforeach; ?>
            </div>

            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Aliment</th>
                                <th>Catégorie</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="aliments-table">
                        <?php if (empty($aliments)): ?>
                            <tr><td colspan="4" class="text-center" style="padding:32px;color:var(--ink-muted)">Aucun aliment enregistré</td></tr>
                        <?php endif; ?>
                        <?php
                        $typeColors = [
                            'viande'  => 'badge-red',
                            'poisson' => 'badge-blue',
                            'volaille'=> 'badge-gold',
                            'legume'  => 'badge-green',
                        ];
                        ?>
                        <?php foreach ($aliments as $a): ?>
                            <tr data-type="<?= $a['type_aliment'] ?>">
                                <td style="color:var(--ink-muted);font-size:.8rem"><?= $a['id'] ?></td>
                                <td><strong><?= esc($a['nom_aliment']) ?></strong></td>
                                <td>
                                    <span class="badge <?= $typeColors[$a['type_aliment']] ?? 'badge-gray' ?>" style="text-transform:capitalize">
                                        <?= esc($a['type_aliment']) ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="<?= site_url('/admin/aliments/delete/' . $a['id']) ?>" method="post"
                                          onsubmit="return confirm('Supprimer cet aliment ?')">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-danger btn-sm">🗑️ Supprimer</button>
                                    </form>
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
            <h3 style="font-size:1.1rem">Ajouter un aliment</h3>
            <button class="modal-close" onclick="closeModal('modal-create')">✕</button>
        </div>
        <form action="<?= site_url('/admin/aliments/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nom de l'aliment *</label>
                    <input class="form-control" name="nom_aliment" placeholder="Ex: Saumon grillé" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Catégorie *</label>
                    <select class="form-control" name="type_aliment" required>
                        <option value="">Choisir une catégorie...</option>
                        <?php foreach ($types as $type): ?>
                        <option value="<?= $type ?>" style="text-transform:capitalize"><?= ucfirst($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-create')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
function closeOnBackdrop(e,id) { if(e.target===e.currentTarget) closeModal(id); }

function filterType(type, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('active');
        b.classList.add('btn-outline');
        b.classList.remove('btn-secondary');
    });
    btn.classList.add('active', 'btn-secondary');
    btn.classList.remove('btn-outline');

    document.querySelectorAll('#aliments-table tr').forEach(row => {
        if (type === 'all' || row.dataset.type === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
</body>
</html>
