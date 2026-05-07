<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif Programme — RégimeSport</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500;600;700&display=swap');
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: #fff; color: #1a1a1a; font-size: 14px; }
        .page { max-width: 800px; margin: 0 auto; padding: 40px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 24px; border-bottom: 3px solid #40916c; margin-bottom: 32px; }
        .logo-block h2 { font-family: 'DM Serif Display', serif; color: #1a3a2a; font-size: 1.6rem; }
        .logo-block p  { font-size: .8rem; color: #8a8a8a; margin-top: 2px; }
        .date-block { text-align: right; font-size: .8rem; color: #8a8a8a; }
        .date-block strong { display: block; font-size: 1.1rem; color: #1a1a1a; }

        /* Badge Gold */
        .gold-badge { display: inline-block; background: #c8973a; color: white; font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-left: 8px; }

        /* Sections */
        .section { margin-bottom: 28px; }
        .section-title {
            font-size: .75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .08em; color: #2d6a4f; margin-bottom: 12px;
            padding-bottom: 6px; border-bottom: 1px solid #e4e9e6;
        }

        /* Info grid */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .info-item { display: flex; flex-direction: column; gap: 3px; }
        .info-label { font-size: .68rem; text-transform: uppercase; color: #8a8a8a; font-weight: 600; letter-spacing: .05em; }
        .info-value { font-size: .95rem; color: #1a1a1a; font-weight: 500; }

        /* IMC Bar */
        .imc-box { background: linear-gradient(135deg, #1a3a2a, #2d6a4f); border-radius: 12px; padding: 24px; color: white; display: flex; align-items: center; gap: 24px; }
        .imc-number { font-family: 'DM Serif Display', serif; font-size: 3.5rem; color: white; line-height: 1; }
        .imc-info h4 { color: white; font-size: 1rem; margin-bottom: 4px; }
        .imc-info p  { font-size: .82rem; opacity: .8; }

        /* Composition table */
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f0faf4; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #2d6a4f; padding: 10px 14px; text-align: left; border-bottom: 2px solid #d8f3dc; }
        tbody td { padding: 10px 14px; border-bottom: 1px solid #e4e9e6; font-size: .85rem; color: #4a4a4a; }
        tbody tr:last-child td { border-bottom: none; }

        /* Progress bar */
        .pct-bar { height: 7px; background: #e4e9e6; border-radius: 99px; overflow: hidden; margin-top: 4px; }
        .pct-fill { height: 100%; background: linear-gradient(90deg, #40916c, #74c69d); border-radius: 99px; }

        /* Price box */
        .price-box { background: #1a3a2a; color: white; border-radius: 12px; padding: 24px; text-align: center; }
        .price-total { font-family: 'DM Serif Display', serif; font-size: 2.8rem; color: white; line-height: 1; }
        .price-label { font-size: .8rem; opacity: .7; margin-top: 6px; }
        .price-original { font-size: .9rem; opacity: .5; text-decoration: line-through; margin-top: 4px; }
        .gold-discount-box { display: inline-block; background: #c8973a; color: white; font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-top: 6px; }

        /* Footer */
        .footer { margin-top: 48px; padding-top: 16px; border-top: 1px solid #e4e9e6; display: flex; justify-content: space-between; align-items: center; font-size: .75rem; color: #8a8a8a; }

        /* Print button */
        .print-btn { position: fixed; bottom: 24px; right: 24px; background: #40916c; color: white; border: none; padding: 14px 24px; border-radius: 10px; font-size: .9rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 20px rgba(64,145,108,.4); display: flex; align-items: center; gap: 8px; font-family: 'DM Sans', sans-serif; }
        .print-btn:hover { background: #2d6a4f; }
        @media print { .print-btn { display: none; } body { padding: 0; } .page { padding: 20px; } }
    </style>
</head>
<body>

<button class="print-btn" onclick="window.print()">
    🖨️ Imprimer / Sauvegarder PDF
</button>

<div class="page">

    <!-- ── EN-TÊTE ── -->
    <div class="header">
        <div class="logo-block">
            <h2>💚 RégimeSport</h2>
            <p>Récapitulatif de votre programme personnalisé</p>
        </div>
        <div class="date-block">
            <span>Date d'achat</span>
            <strong><?= date('d/m/Y', strtotime($prog['date_achat'])) ?></strong>
            <div style="margin-top:6px;font-size:.7rem">N° <?= str_pad($prog['id'], 6, '0', STR_PAD_LEFT) ?></div>
        </div>
    </div>

    <!-- ── INFORMATIONS CLIENT ── -->
    <div class="section">
        <div class="section-title">👤 Informations du client</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Nom complet</span>
                <span class="info-value">
                    <?= esc($prog['user_nom']) ?>
                    <?php if ($prog['is_gold']): ?>
                        <span class="gold-badge">⭐ GOLD</span>
                    <?php endif; ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value"><?= esc($prog['user_email']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Genre</span>
                <span class="info-value"><?= $prog['genre'] === 'M' ? 'Masculin' : 'Féminin' ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Poids visé</span>
                <span class="info-value">
                    <?= $prog['poids_objectif_vise'] ? $prog['poids_objectif_vise'] . ' kg' : '—' ?>
                </span>
            </div>
        </div>
    </div>

    <!-- ── IMC ── -->
    <?php if ($imc): ?>
    <div class="section">
        <div class="section-title">📊 Indice de Masse Corporelle (IMC)</div>
        <div class="imc-box">
            <div class="imc-number"><?= $imc['imc'] ?></div>
            <div class="imc-info">
                <h4><?= esc($imc['categorie']) ?></h4>
                <p>Taille : <?= $prog['taille'] ?> m &nbsp;|&nbsp; Poids : <?= $prog['poids_actuel'] ?> kg</p>
                <p style="margin-top:8px"><?= esc($imc['conseil']) ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── RÉGIME ── -->
    <div class="section">
        <div class="section-title">🥗 Régime alimentaire</div>
        <div class="info-grid" style="margin-bottom:16px">
            <div class="info-item">
                <span class="info-label">Nom du régime</span>
                <span class="info-value" style="font-weight:700;color:#2d6a4f"><?= esc($prog['nom_regime']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Durée</span>
                <span class="info-value"><?= $prog['duree_jours'] ?> jours</span>
            </div>
            <div class="info-item">
                <span class="info-label">Prix journalier</span>
                <span class="info-value"><?= number_format($prog['prix_journalier'], 0, ',', ' ') ?> Ar/jour</span>
            </div>
            <div class="info-item">
                <span class="info-label">Impact sur le poids</span>
                <span class="info-value" style="color:<?= $prog['poids_impact'] < 0 ? '#e63946' : '#40916c' ?>">
                    <?= $prog['poids_impact'] > 0 ? '+' : '' ?><?= $prog['poids_impact'] ?> kg
                </span>
            </div>
        </div>

        <?php if (!empty($prog['regime_desc'])): ?>
        <p style="font-size:.85rem;color:#4a4a4a;padding:12px;background:#f0faf4;border-radius:8px;margin-bottom:16px">
            <?= esc($prog['regime_desc']) ?>
        </p>
        <?php endif; ?>

        <!-- Composition -->
        <?php if (!empty($prog['composition'])): ?>
        <table>
            <thead>
                <tr>
                    <th>Aliment</th>
                    <th>Catégorie</th>
                    <th>Proportion</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($prog['composition'] as $comp): ?>
                <tr>
                    <td><strong><?= esc($comp['nom_aliment']) ?></strong></td>
                    <td>
                        <span style="text-transform:capitalize;color:#40916c;font-weight:500">
                            <?= esc($comp['type_aliment']) ?>
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <span style="font-weight:600;min-width:36px"><?= $comp['pourcentage'] ?>%</span>
                            <div class="pct-bar" style="flex:1">
                                <div class="pct-fill" style="width:<?= $comp['pourcentage'] ?>%"></div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- ── ACTIVITÉ ── -->
    <div class="section">
        <div class="section-title">🏃 Activité sportive</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Activité choisie</span>
                <span class="info-value" style="font-weight:700;color:#2d6a4f"><?= esc($prog['nom_activite']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Durée du programme</span>
                <span class="info-value"><?= $prog['activite_duree'] ?> jours</span>
            </div>
        </div>
    </div>

    <!-- ── RÉCAPITULATIF FINANCIER ── -->
    <div class="section">
        <div class="section-title">💰 Récapitulatif financier</div>
        <?php
            $prixBrut  = $prog['prix_journalier'] * $prog['duree_jours'];
            $prixPaye  = (float)$prog['prix_total_paye'];
            $remise    = $prixBrut - $prixPaye;
            $isGold    = (bool)$prog['is_gold'];
        ?>
        <div class="price-box">
            <?php if ($isGold && $remise > 0): ?>
                <div class="price-original"><?= number_format($prixBrut, 0, ',', ' ') ?> Ar</div>
                <div class="gold-discount-box">⭐ Remise GOLD — 15% économisés</div>
                <div style="font-size:.8rem;opacity:.6;margin-top:6px;margin-bottom:8px">
                    Économie : <?= number_format($remise, 0, ',', ' ') ?> Ar
                </div>
            <?php endif; ?>
            <div class="price-total"><?= number_format($prixPaye, 0, ',', ' ') ?> Ar</div>
            <div class="price-label">Montant total payé</div>
        </div>
    </div>

    <!-- ── CONSEILS ── -->
    <div class="section">
        <div class="section-title">📝 Conseils importants</div>
        <div style="background:#f0faf4;border-radius:8px;padding:16px;font-size:.82rem;color:#4a4a4a;line-height:1.8">
            <strong style="color:#1a3a2a">Pour maximiser vos résultats :</strong><br>
            • Respectez scrupuleusement les proportions alimentaires indiquées<br>
            • Pratiquez votre activité sportive au minimum 3 fois par semaine<br>
            • Hydratez-vous suffisamment (1,5 à 2 litres d'eau par jour)<br>
            • Suivez régulièrement l'évolution de votre poids<br>
            • Consultez un professionnel de santé avant de commencer
        </div>
    </div>

    <!-- ── FOOTER ── -->
    <div class="footer">
        <div>
            <strong>RégimeSport Madagascar</strong> — Votre santé, notre priorité
        </div>
        <div>
            Document généré le <?= date('d/m/Y à H:i') ?><br>
            Ce document est votre preuve d'achat.
        </div>
    </div>
</div>

</body>
</html>
