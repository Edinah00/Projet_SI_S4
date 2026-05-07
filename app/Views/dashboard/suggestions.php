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
            <span class="topbar-title">Trouver mon programme</span>
        </div>

        <div class="page-body">
            <!-- Sélecteur d'objectif -->
            <div class="card mb-24">
                <div class="card-header">
                    <span class="card-title">Quel est votre objectif ?</span>
                </div>
                <div class="card-body">
                    <div class="objective-selector">
                        <div class="objective-card" onclick="selectObjectif('reduire', this)">
                            <div class="icon">📉</div>
                            <h4>Réduire le poids</h4>
                            <p>Perdre de la masse grasse</p>
                        </div>
                        <div class="objective-card" onclick="selectObjectif('augmenter', this)">
                            <div class="icon">💪</div>
                            <h4>Augmenter la masse</h4>
                            <p>Gagner du muscle</p>
                        </div>
                        <div class="objective-card" onclick="selectObjectif('ideal', this)">
                            <div class="icon">⚖️</div>
                            <h4>IMC Idéal</h4>
                            <p>Maintenir l'équilibre</p>
                        </div>
                    </div>

                    <?php if (session()->get('isGold')): ?>
                    <div class="alert alert-gold">
                        ⭐ En tant que membre <strong>GOLD</strong>, vous bénéficiez d'une remise de <strong>15%</strong> sur tous les régimes !
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Résultats (cachés au départ) -->
            <div id="results" style="display:none">
                <div id="loading" class="text-center" style="padding:40px">
                    <div class="spinner" style="border-color:rgba(64,145,108,.2);border-top-color:var(--green-500);width:36px;height:36px;border-width:3px"></div>
                    <p style="margin-top:12px;color:var(--ink-muted)">Recherche en cours...</p>
                </div>

                <div id="content" style="display:none">
                    <!-- Régimes -->
                    <h2 class="mb-16">Régimes recommandés</h2>
                    <div class="regime-grid mb-32" id="regimes-container"></div>

                    <!-- Activités -->
                    <h2 class="mb-16">Activités sportives</h2>
                    <div class="regime-grid mb-32" id="activites-container"></div>

                    <!-- Formulaire d'achat -->
                    <div class="card" id="achat-form" style="display:none">
                        <div class="card-header">
                            <span class="card-title">✅ Confirmer mon programme</span>
                        </div>
                        <div class="card-body">
                            <div class="grid-2 mb-24">
                                <div>
                                    <div class="form-label mb-8">Régime sélectionné</div>
                                    <div id="selected-regime-name" style="font-weight:600;color:var(--green-700)">—</div>
                                </div>
                                <div>
                                    <div class="form-label mb-8">Activité sélectionnée</div>
                                    <div id="selected-activite-name" style="font-weight:600;color:var(--green-700)">—</div>
                                </div>
                            </div>

                            <div class="d-flex align-center gap-16 mb-24">
                                <div>
                                    <div class="form-label">Prix total</div>
                                    <div class="price-tag" id="selected-prix">—</div>
                                </div>
                                <div id="prix-original-wrap" style="display:none">
                                    <div class="price-original" id="selected-prix-original"></div>
                                    <span class="gold-discount">-15% GOLD</span>
                                </div>
                            </div>

                            <form action="<?= site_url('/programme/acheter') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_regime"  id="input-regime">
                                <input type="hidden" name="id_activite" id="input-activite">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    💳 Acheter ce programme
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF_TOKEN  = '<?= csrf_hash() ?>';
const CSRF_NAME   = '<?= csrf_token() ?>';
const IS_GOLD     = <?= session()->get('isGold') ? 'true' : 'false' ?>;

let selectedRegime  = null;
let selectedActivite= null;

function selectObjectif(objectif, el) {
    document.querySelectorAll('.objective-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    loadSuggestions(objectif);
}

async function loadSuggestions(objectif) {
    document.getElementById('results').style.display = 'block';
    document.getElementById('loading').style.display = 'block';
    document.getElementById('content').style.display = 'none';
    document.getElementById('achat-form').style.display = 'none';

    const data = new FormData();
    data.append(CSRF_NAME, CSRF_TOKEN);
    data.append('objectif', objectif);

    try {
        const res  = await fetch('<?= site_url("/suggestions/get") ?>', { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const json = await res.json();

        if (json.success) {
            renderRegimes(json.regimes);
            renderActivites(json.activites);
            document.getElementById('loading').style.display = 'none';
            document.getElementById('content').style.display = 'block';
        }
    } catch(e) {
        console.error(e);
    }
}

function renderRegimes(regimes) {
    const container = document.getElementById('regimes-container');
    container.innerHTML = regimes.map(r => `
        <div class="regime-card" id="regime-${r.id}" onclick="selectRegime(${r.id}, '${escHtml(r.nom_regime)}', ${r.prix_final}, ${r.prix_brut})">
            <div class="regime-card-header">
                <h4>${escHtml(r.nom_regime)}</h4>
                <div style="font-size:.8rem;opacity:.7;margin-top:4px">⏱ ${r.duree_jours} jours • Impact: ${r.poids_impact > 0 ? '+' : ''}${r.poids_impact} kg</div>
            </div>
            <div class="regime-card-body">
                <p style="font-size:.82rem;margin-bottom:12px">${escHtml(r.description || '')}</p>
                <div class="d-flex align-center gap-8">
                    <div class="price-tag">${formatNum(r.prix_final)} Ar</div>
                    ${r.remise > 0 ? `<div><div class="price-original">${formatNum(r.prix_brut)} Ar</div><span class="gold-discount">-15% GOLD</span></div>` : ''}
                </div>
                <div style="margin-top:4px;font-size:.75rem;color:var(--ink-muted)">${formatNum(r.prix_journalier)} Ar/jour</div>
                <div class="btn btn-secondary btn-sm" style="margin-top:12px;display:inline-flex">Sélectionner</div>
            </div>
        </div>
    `).join('');
}

function renderActivites(activites) {
    const container = document.getElementById('activites-container');
    container.innerHTML = activites.map(a => `
        <div class="regime-card" id="activite-${a.id}" onclick="selectActivite(${a.id}, '${escHtml(a.nom_activite)}')">
            <div class="regime-card-header" style="background:var(--green-700)">
                <h4>${escHtml(a.nom_activite)}</h4>
                <div style="font-size:.8rem;opacity:.7;margin-top:4px">⏱ ${a.duree_jours} jours • Impact: ${a.poids_impact > 0 ? '+' : ''}${a.poids_impact} kg/j</div>
            </div>
            <div class="regime-card-body">
                <div class="btn btn-secondary btn-sm" style="margin-top:4px;display:inline-flex">Choisir</div>
            </div>
        </div>
    `).join('');
}

function selectRegime(id, nom, prix, prixBrut) {
    document.querySelectorAll('[id^="regime-"]').forEach(el => el.style.border = '1px solid var(--border)');
    document.getElementById('regime-' + id).style.border = '2px solid var(--green-500)';

    selectedRegime = { id, nom, prix, prixBrut };
    document.getElementById('selected-regime-name').textContent = nom;
    document.getElementById('selected-prix').textContent = formatNum(prix) + ' Ar';
    document.getElementById('input-regime').value = id;

    const origWrap = document.getElementById('prix-original-wrap');
    if (prixBrut !== prix) {
        document.getElementById('selected-prix-original').textContent = formatNum(prixBrut) + ' Ar';
        origWrap.style.display = 'block';
    } else {
        origWrap.style.display = 'none';
    }

    updateAchatForm();
}

function selectActivite(id, nom) {
    document.querySelectorAll('[id^="activite-"]').forEach(el => el.style.border = '1px solid var(--border)');
    document.getElementById('activite-' + id).style.border = '2px solid var(--green-500)';

    selectedActivite = { id, nom };
    document.getElementById('selected-activite-name').textContent = nom;
    document.getElementById('input-activite').value = id;

    updateAchatForm();
}

function updateAchatForm() {
    if (selectedRegime && selectedActivite) {
        document.getElementById('achat-form').style.display = 'block';
        document.getElementById('achat-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function formatNum(n) {
    return Math.round(n).toLocaleString('fr-FR');
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
</script>
</body>
</html>
