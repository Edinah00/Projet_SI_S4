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
            <span class="topbar-title">🎫 Codes de Recharge</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-create')">+ Créer un code</button>
        </div>

        <div class="page-body">
            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <!-- Stats rapides -->
            <div class="stats-grid mb-24" style="grid-template-columns:repeat(4,1fr)">
                <?php
                $totalCodes   = count($codes);
                $validés      = array_filter($codes, fn($c) => $c['est_valide']);
                $utilisés     = array_filter($codes, fn($c) => $c['est_utilise']);
                $enAttente    = array_filter($codes, fn($c) => !$c['est_valide'] && !$c['est_utilise']);
                ?>
                <div class="stat-card">
                    <div class="stat-icon blue"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div>
                    <div class="stat-value"><?= $totalCodes ?></div>
                    <div class="stat-label">Total codes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <div class="stat-value"><?= count($validés) ?></div>
                    <div class="stat-label">Codes validés</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon gold"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    <div class="stat-value"><?= count($utilisés) ?></div>
                    <div class="stat-label">Codes utilisés</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                    <div class="stat-value"><?= count($enAttente) ?></div>
                    <div class="stat-label">En attente validation</div>
                </div>
            </div>

            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Valeur</th>
                                <th>Statut</th>
                                <th>Utilisé ?</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($codes)): ?>
                            <tr><td colspan="5" class="text-center" style="padding:32px;color:var(--ink-muted)">Aucun code enregistré</td></tr>
                        <?php endif; ?>
                        <?php foreach ($codes as $c): ?>
                            <tr>
                                <td>
                                    <code style="background:var(--green-50);padding:4px 10px;border-radius:6px;font-weight:700;color:var(--green-700);letter-spacing:.05em;font-size:.85rem">
                                        <?= esc($c['code']) ?>
                                    </code>
                                </td>
                                <td><strong style="color:var(--green-700)"><?= number_format($c['valeur'], 0, ',', ' ') ?> Ar</strong></td>
                                <td>
                                    <?php if ($c['est_valide']): ?>
                                        <span class="badge badge-green">✅ Validé</span>
                                    <?php else: ?>
                                        <span class="badge badge-red">⏳ En attente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($c['est_utilise']): ?>
                                        <span class="badge badge-gray">Utilisé</span>
                                    <?php else: ?>
                                        <span class="badge badge-blue">Disponible</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-8">
                                        <?php if (!$c['est_valide'] && !$c['est_utilise']): ?>
                                        <form action="<?= site_url('/admin/codes/valider/' . $c['id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-primary btn-sm">✅ Valider</button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if (!$c['est_utilise']): ?>
                                        <form action="<?= site_url('/admin/codes/delete/' . $c['id']) ?>" method="post"
                                              onsubmit="return confirm('Supprimer ce code ?')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-danger btn-sm">🗑️</button>
                                        </form>
                                        <?php endif; ?>
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

<!-- Modal Créer un code -->
<div class="modal-backdrop" id="modal-create" onclick="closeOnBackdrop(event,'modal-create')">
    <div class="modal">
        <div class="modal-header">
            <h3 style="font-size:1.1rem">Créer un code de recharge</h3>
            <button class="modal-close" onclick="closeModal('modal-create')">✕</button>
        </div>
        <form action="<?= site_url('/admin/codes/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Code *</label>
                    <div class="d-flex gap-8">
                        <input class="form-control" name="code" id="code-input" placeholder="Ex: PROMO2024" maxlength="15"
                               style="text-transform:uppercase;letter-spacing:.05em;font-weight:600" required>
                        <button type="button" class="btn btn-secondary" onclick="generateCode()" style="white-space:nowrap">
                            🎲 Générer
                        </button>
                    </div>
                    <div style="font-size:.75rem;color:var(--ink-muted);margin-top:4px">Maximum 15 caractères, lettres et chiffres</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Valeur (Ar) *</label>
                    <input class="form-control" type="number" name="valeur" placeholder="Ex: 25000" min="1" required>
                </div>
                <div class="alert alert-info" style="margin-top:8px">
                    ℹ️ Le code sera créé en statut <strong>"En attente"</strong>. Vous devrez le valider manuellement pour qu'il soit utilisable.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-create')">Annuler</button>
                <button type="submit" class="btn btn-primary">Créer le code</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
function closeOnBackdrop(e,id) { if(e.target===e.currentTarget) closeModal(id); }

function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 12; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('code-input').value = code;
}

// Majuscules automatiques
document.getElementById('code-input').addEventListener('input', function() {
    const pos = this.selectionStart;
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    this.setSelectionRange(pos, pos);
});
</script>
</body>
</html>
