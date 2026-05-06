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
            <div class="logo-icon">
                <svg viewBox="0 0 24 24" width="18" height="18"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div>
                <div class="brand-name">TP Releve</div>
                <div class="brand-sub">v1.0.0</div>
            </div>
        </div>

        <div class="sidebar-section">Navigation</div>
        <a href="<?= site_url('/etudiants') ?>" class="nav-item active">Liste des etudiants</a>
        <a href="<?= site_url('/notes/create') ?>" class="nav-item">Saisie des notes</a>

        <div class="sidebar-bottom">
            <a href="<?= site_url('/logout') ?>" class="user-row">
                <div class="avatar">AD</div>
                <div class="user-info">
                    <div class="name">Admin TP</div>
                    <div class="role">Deconnexion</div>
                </div>
            </a>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="topbar-title">Gestion des etudiants</div>
            <div class="topbar-search">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" placeholder="Rechercher..." />
            </div>
            <div class="topbar-actions">
                <button class="icon-btn"><svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg><span class="notif-dot"></span></button>
                <button class="icon-btn"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg></button>
            </div>
        </div>

        <div class="content">
            <div class="page-header">
                <div>
                    <h2>Liste des etudiants</h2>
                    <div class="breadcrumb">Accueil / <span>Etudiants</span></div>
                </div>
                <a href="<?= site_url('/notes/create') ?>" class="btn btn-primary">Ajouter une note</a>
            </div>

            <div class="kpi-grid">
                <div class="kpi-card"><div class="kpi-label">Etudiants</div><div class="kpi-value"><?= count($etudiants) ?></div></div>
                <div class="kpi-card"><div class="kpi-label">Parcours</div><div class="kpi-value">3</div></div>
                <div class="kpi-card"><div class="kpi-label">Semestres</div><div class="kpi-value">S3-S4</div></div>
                <div class="kpi-card"><div class="kpi-label">Regle</div><div class="kpi-value">Max note</div></div>
            </div>

            <div class="toolbar">
                <div class="toolbar-left">
                    <div class="search-box">
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" placeholder="Rechercher un etudiant..." />
                    </div>
                    <select class="filter-select">
                        <option>Tous les parcours</option>
                    </select>
                </div>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Etudiant</th>
                            <th>Parcours</th>
                            <th>Moyenne S3</th>
                            <th>Moyenne S4</th>
                            <th>Moyenne L2</th>
                            <th>Mention</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($etudiants as $etudiant): ?>
                            <tr>
                                <td><span class="mono"><?= esc($etudiant['matricule']) ?></span></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="avatar-sm"><?= esc(strtoupper(substr($etudiant['prenom'], 0, 1) . substr($etudiant['nom'], 0, 1))) ?></div>
                                        <div class="stack-sm">
                                            <strong><?= esc($etudiant['nom'] . ' ' . $etudiant['prenom']) ?></strong>
                                            <div class="muted"><?= esc($etudiant['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-blue"><?= strtoupper(esc($etudiant['parcours'])) ?></span></td>
                                <td><?= number_format($etudiant['moyenne_s3'], 2, ',', ' ') ?></td>
                                <td><?= number_format($etudiant['moyenne_s4'], 2, ',', ' ') ?></td>
                                <td><?= number_format($etudiant['moyenne_l2'], 2, ',', ' ') ?></td>
                                <td><?= esc($etudiant['mention']) ?></td>
                                <td>
                                    <div class="td-actions">
                                        <a href="<?= site_url('/etudiants/' . $etudiant['id']) ?>" class="action-btn" title="Voir">
                                            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        <a href="<?= site_url('/etudiants/' . $etudiant['id'] . '/notes') ?>" class="action-btn" title="Notes">
                                            <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                        </a>
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
</body>
</html>
