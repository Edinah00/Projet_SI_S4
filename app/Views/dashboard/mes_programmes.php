<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> — RégimeSport</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>
<div class="app-wrapper">
    <?= view('partials/sidebar') ?>

    <div class="main-content">
        <div class="topbar">
            <span class="topbar-title">Mes Programmes</span>
            <a href="<?= base_url('/suggestions') ?>" class="btn btn-primary btn-sm">+ Nouveau programme</a>
        </div>

        <div class="page-body">
            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?php if (empty($programmes)): ?>
            <div class="card">
                <div class="card-body text-center" style="padding:60px">
                    <div style="font-size:4rem;margin-bottom:16px">🌿</div>
                    <h2 class="mb-8" style="font-size:1.4rem">Aucun programme pour l'instant</h2>
                    <p class="mb-24">Commencez par sélectionner un régime et une activité sportive adaptés à votre objectif.</p>
                    <a href="<?= base_url('/suggestions') ?>" class="btn btn-primary btn-lg">
                        🔍 Trouver mon programme
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="regime-grid">
                <?php foreach ($programmes as $prog): ?>
                <div class="card" style="overflow:visible">
                    <div class="regime-card-header" style="background:var(--green-900);padding:20px;border-radius:12px 12px 0 0">
                        <h4 style="color:white;margin-bottom:4px"><?= esc($prog['nom_regime']) ?></h4>
                        <div style="font-size:.75rem;color:var(--green-300)">
                            📅 Acheté le <?= date('d/m/Y', strtotime($prog['date_achat'])) ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;font-size:.82rem">
                            <div>
                                <div style="color:var(--ink-muted);margin-bottom:2px">Durée</div>
                                <strong><?= $prog['duree_jours'] ?> jours</strong>
                            </div>
                            <div>
                                <div style="color:var(--ink-muted);margin-bottom:2px">Activité</div>
                                <strong><?= esc($prog['nom_activite']) ?></strong>
                            </div>
                            <div>
                                <div style="color:var(--ink-muted);margin-bottom:2px">Impact visé</div>
                                <strong style="color:<?= $prog['poids_impact'] < 0 ? 'var(--red)' : 'var(--green-500)' ?>">
                                    <?= $prog['poids_impact'] > 0 ? '+' : '' ?><?= $prog['poids_impact'] ?> kg
                                </strong>
                            </div>
                            <div>
                                <div style="color:var(--ink-muted);margin-bottom:2px">Prix payé</div>
                                <strong style="color:var(--green-700)"><?= number_format($prog['prix_total_paye'], 0, ',', ' ') ?> Ar</strong>
                            </div>
                        </div>

                        <a href="<?= base_url('/programme/pdf/' . $prog['id']) ?>"
                           class="btn btn-secondary btn-full"
                           target="_blank">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Télécharger le récapitulatif PDF
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
