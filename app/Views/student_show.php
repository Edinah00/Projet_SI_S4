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
            <div class="topbar-title">Releve de notes</div>
            <div class="topbar-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" placeholder="Rechercher..." /></div>
        </div>

        <div class="content">
            <div class="page-header">
                <div>
                    <h2><?= esc($etudiant['nom'] . ' ' . $etudiant['prenom']) ?></h2>
                    <div class="breadcrumb"><?= esc($etudiant['matricule']) ?> / <span><?= esc(strtoupper($etudiant['parcours'])) ?></span></div>
                </div>
                <a href="<?= site_url('/etudiants') ?>" class="btn btn-secondary">Retour</a>
            </div>

            <div class="kpi-grid">
                <div class="kpi-card"><div class="kpi-label">Moyenne S3</div><div class="kpi-value"><?= number_format($semestre3['moyenne'], 2, ',', ' ') ?></div></div>
                <div class="kpi-card"><div class="kpi-label">Moyenne S4</div><div class="kpi-value"><?= number_format($semestre4['moyenne'], 2, ',', ' ') ?></div></div>
                <div class="kpi-card"><div class="kpi-label">Moyenne L2</div><div class="kpi-value"><?= number_format($global['moyenne'], 2, ',', ' ') ?></div></div>
            </div>

            <div class="card" style="margin-bottom:24px">
                <div class="tabs">
                    <a class="tab <?= $selectedView === 's3' ? 'active' : '' ?>" href="<?= site_url('/etudiants/' . $etudiant['id']) ?>?vue=s3">S3</a>
                    <a class="tab <?= $selectedView === 's4' ? 'active' : '' ?>" href="<?= site_url('/etudiants/' . $etudiant['id']) ?>?vue=s4">S4</a>
                    <a class="tab <?= $selectedView === 'l2' ? 'active' : '' ?>" href="<?= site_url('/etudiants/' . $etudiant['id']) ?>?vue=l2">L2</a>
                </div>

                <?php $releve = $selectedView === 's4' ? $semestre4 : ($selectedView === 'l2' ? $global : $semestre3); ?>

                <div class="card-header">
                    <div class="card-title"><?= $selectedView === 'l2' ? 'Synthese L2' : 'Releve du ' . strtoupper($selectedView) ?></div>
                    <div class="panel-note">Les matieres optionnelles prennent la meilleure note.</div>
                </div>

                <?php if ($selectedView === 'l2'): ?>
                    <div class="l2-stack">
                        <div class="table-card l2-block" style="box-shadow:none">
                            <table>
                                <thead>
                                    <tr>
                                        <th>UE</th>
                                        <th>Intitule</th>
                                        <th>Credits</th>
                                        <th>Note/20</th>
                                        <th>Resultat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($semestre3['rows'] as $row): ?>
                                        <tr>
                                            <td><span class="mono"><?= esc($row['code']) ?></span></td>
                                            <td><strong><?= esc($row['intitule']) ?></strong><?php if (! empty($row['label'])): ?><div class="muted"><?= esc($row['label']) ?></div><?php endif; ?></td>
                                            <td><?= esc($row['credits']) ?></td>
                                            <td><?= number_format($row['note'], 2, ',', ' ') ?></td>
                                            <td><?= esc($row['resultat']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">SEMESTRE 3</th>
                                        <th><?= esc($semestre3['credits']) ?></th>
                                        <th><?= number_format($semestre3['moyenne'], 2, ',', ' ') ?></th>
                                        <th><?= esc($semestre3['mention']) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="table-card l2-block" style="box-shadow:none">
                            <table>
                                <thead>
                                    <tr>
                                        <th>UE</th>
                                        <th>Intitule</th>
                                        <th>Credits</th>
                                        <th>Note/20</th>
                                        <th>Resultat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($semestre4['rows'] as $row): ?>
                                        <tr>
                                            <td><span class="mono"><?= esc($row['code']) ?></span></td>
                                            <td><strong><?= esc($row['intitule']) ?></strong><?php if (! empty($row['label'])): ?><div class="muted"><?= esc($row['label']) ?></div><?php endif; ?></td>
                                            <td><?= esc($row['credits']) ?></td>
                                            <td><?= number_format($row['note'], 2, ',', ' ') ?></td>
                                            <td><?= esc($row['resultat']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">SEMESTRE 4</th>
                                        <th><?= esc($semestre4['credits']) ?></th>
                                        <th><?= number_format($semestre4['moyenne'], 2, ',', ' ') ?></th>
                                        <th><?= esc($semestre4['mention']) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="summary-box l2-summary">
                            <p><strong>Resultat :</strong> Credits: <?= esc($global['credits']) ?></p>
                            <p><strong>Moyenne generale:</strong> <?= number_format($global['moyenne'], 2, ',', ' ') ?></p>
                            <p><strong>Mention:</strong> <?= esc($global['mention']) ?></p>
                            <p><strong>Decision:</strong> <?= esc($global['resultat']) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="table-card" style="box-shadow:none">
                        <table>
                            <thead>
                                <tr>
                                    <th>UE</th>
                                    <th>Intitule</th>
                                    <th>Credits</th>
                                    <th>Note/20</th>
                                    <th>Resultat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($releve['rows'] as $row): ?>
                                    <tr>
                                        <td><span class="mono"><?= esc($row['code']) ?></span></td>
                                        <td><strong><?= esc($row['intitule']) ?></strong><?php if (! empty($row['label'])): ?><div class="muted"><?= esc($row['label']) ?></div><?php endif; ?></td>
                                        <td><?= esc($row['credits']) ?></td>
                                        <td><?= number_format($row['note'], 2, ',', ' ') ?></td>
                                        <td><?= esc($row['resultat']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Total du semestre</th>
                                    <th><?= esc($releve['credits']) ?></th>
                                    <th><?= number_format($releve['moyenne'], 2, ',', ' ') ?></th>
                                    <th><?= esc($releve['mention']) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="summary-box">
                        <p><strong>Credits:</strong> <?= esc($releve['credits']) ?></p>
                        <p><strong>Moyenne generale:</strong> <?= number_format($releve['moyenne'], 2, ',', ' ') ?></p>
                        <p><strong>Mention:</strong> <?= esc($releve['mention']) ?></p>
                        <p><strong>Decision:</strong> <?= esc($releve['resultat']) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Historique des notes</div>
                        <div class="panel-note">Toutes les saisies restent visibles.</div>
                    </div>
                    <a class="btn btn-secondary btn-sm" href="<?= site_url('/etudiants/' . $etudiant['id'] . '/notes') ?>">Gerer les notes</a>
                </div>
                <div class="table-card" style="box-shadow:none">
                    <table>
                        <thead>
                            <tr>
                                <th>Semestre</th>
                                <th>Code</th>
                                <th>Matiere</th>
                                <th>Note</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notes as $note): ?>
                                <tr>
                                    <td>S<?= esc($note['semestre']) ?></td>
                                    <td><span class="mono"><?= esc($note['code']) ?></span></td>
                                    <td><?= esc($note['intitule']) ?></td>
                                    <td><?= number_format((float) $note['note'], 2, ',', ' ') ?></td>
                                    <td><?= esc($note['date_saisie']) ?></td>
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
