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
            <span class="topbar-title">Mon Porte-monnaie</span>
        </div>

        <div class="page-body">
            <!-- Solde Hero -->
            <div class="wallet-hero mb-24">
                <div style="font-size:.8rem;text-transform:uppercase;letter-spacing:.1em;opacity:.7;margin-bottom:8px">Solde disponible</div>
                <div class="wallet-balance" id="solde-display"><?= number_format($solde, 0, ',', ' ') ?> Ar</div>
                <div class="wallet-label">Porte-monnaie RégimeSport</div>
            </div>

            <div class="grid-2">
                <!-- Formulaire de recharge -->
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">🎫 Recharger avec un code</span>
                    </div>
                    <div class="card-body">
                        <div id="recharge-alert" style="display:none"></div>

                        <div class="form-group">
                            <label class="form-label">Code de recharge</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                </span>
                                <input class="form-control" type="text" id="code-input"
                                       placeholder="Ex: GOLD2024SPORT"
                                       style="text-transform:uppercase;letter-spacing:.05em;font-weight:600"
                                       maxlength="20">
                            </div>
                            <div style="font-size:.75rem;color:var(--ink-muted);margin-top:4px">
                                Entrez le code tel qu'il vous a été fourni
                            </div>
                        </div>

                        <button class="btn btn-primary btn-full btn-lg" id="btn-recharger" onclick="recharger()">
                            💳 Recharger mon compte
                        </button>

                        <hr class="separator">

                        <div style="font-size:.8rem;color:var(--ink-muted)">
                            <strong>Codes de démonstration disponibles :</strong><br>
                            <code style="background:var(--green-50);padding:2px 6px;border-radius:4px;color:var(--green-700)">GOLD2024SPORT</code> — 50 000 Ar<br>
                            <code style="background:var(--green-50);padding:2px 6px;border-radius:4px;color:var(--green-700)">BIENV2024MG</code> — 25 000 Ar<br>
                            <code style="background:var(--green-50);padding:2px 6px;border-radius:4px;color:var(--green-700)">PROMO15SPORT</code> — 15 000 Ar
                        </div>
                    </div>
                </div>

                <!-- Informations -->
                <div style="display:flex;flex-direction:column;gap:16px">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-8">💡 Comment ça marche ?</h4>
                            <ol style="font-size:.85rem;color:var(--ink-soft);padding-left:20px;line-height:2">
                                <li>Obtenez un code de recharge auprès de votre administrateur</li>
                                <li>Entrez le code dans le champ ci-contre</li>
                                <li>Le montant est crédité instantanément</li>
                                <li>Utilisez votre solde pour acheter des programmes</li>
                            </ol>
                        </div>
                    </div>

                    <?php if (session()->get('isGold')): ?>
                    <div class="alert alert-gold">
                        ⭐ <strong>Avantage GOLD :</strong> Vous bénéficiez d'une remise automatique de 15% sur tous vos achats de programmes !
                    </div>
                    <?php else: ?>
                    <div class="card">
                        <div class="card-body text-center">
                            <div style="font-size:2rem;margin-bottom:8px">⭐</div>
                            <h4 class="mb-8">Passez GOLD !</h4>
                            <p style="font-size:.82rem">Contactez votre administrateur pour obtenir le statut Gold et bénéficier de <strong>-15% sur tous vos programmes</strong>.</p>
                            <button class="btn btn-secondary btn-sm" id="btn-demander-gold" onclick="demanderGold()" style="margin-top:12px">
                                Envoyer ma demande GOLD
                            </button>
                            <div id="gold-request-alert" style="display:none;margin-top:12px"></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <a href="<?= base_url('/suggestions') ?>" class="btn btn-primary btn-lg" style="justify-content:center">
                        🔍 Utiliser mon solde — Trouver un programme
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let CSRF_TOKEN = '<?= csrf_hash() ?>';
const CSRF_NAME  = '<?= csrf_token() ?>';

function syncCsrfToken(json) {
    if (json && json.csrfHash) {
        CSRF_TOKEN = json.csrfHash;
        const input = document.querySelector(`input[name="${CSRF_NAME}"]`);
        if (input) {
            input.value = json.csrfHash;
        }
    }
}

async function recharger() {
    const code = document.getElementById('code-input').value.trim();
    const btn  = document.getElementById('btn-recharger');
    const alertBox = document.getElementById('recharge-alert');

    if (!code) {
        showAlert('Veuillez entrer un code de recharge.', 'error');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Vérification...';
    alertBox.style.display = 'none';

    const data = new FormData();
    data.append(CSRF_NAME, CSRF_TOKEN);
    data.append('code', code);

    try {
        const res  = await fetch('<?= site_url("/portefeuille/recharger") ?>', {
            method: 'POST', body: data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        syncCsrfToken(json);

        if (json.success) {
            showAlert('✅ ' + (json.message || 'Recharge réussie.'), 'success');
            // Mise à jour du solde affiché
            document.getElementById('solde-display').textContent =
                Math.round(json.nouveau_solde).toLocaleString('fr-FR') + ' Ar';
            document.getElementById('code-input').value = '';
        } else {
            showAlert('❌ ' + (json.message || json.error || 'Erreur inconnue.'), 'error');
        }
    } catch(e) {
        showAlert('Erreur réseau. Réessayez.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '💳 Recharger mon compte';
    }
}

function showAlert(message, type) {
    const box = document.getElementById('recharge-alert');
    box.style.display = 'flex';
    box.className = `alert alert-${type === 'error' ? 'error' : 'success'}`;
    box.innerHTML = message;
}

function showGoldAlert(message, type) {
    const box = document.getElementById('gold-request-alert');
    if (!box) return;
    box.style.display = 'flex';
    box.className = `alert alert-${type === 'error' ? 'error' : 'success'}`;
    box.innerHTML = message;
}

// Majuscules automatiques
document.getElementById('code-input').addEventListener('input', function() {
    const pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
});

// Recharger avec Entrée
document.getElementById('code-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') recharger();
});

async function demanderGold() {
    const btn = document.getElementById('btn-demander-gold');
    const alertBox = document.getElementById('gold-request-alert');
    if (!btn || !alertBox) return;

    btn.disabled = true;
    btn.innerHTML = 'Envoi en cours...';
    alertBox.style.display = 'none';

    const data = new FormData();
    data.append(CSRF_NAME, CSRF_TOKEN);

    try {
        const res = await fetch('<?= site_url("/portefeuille/demander-gold") ?>', {
            method: 'POST',
            body: data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        syncCsrfToken(json);

        if (json.success) {
            showGoldAlert('✅ ' + json.message, 'success');
        } else {
            showGoldAlert('❌ ' + (json.message || 'Impossible d\'envoyer la demande.'), 'error');
        }
    } catch (e) {
        showGoldAlert('Erreur réseau. Réessayez.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Envoyer ma demande GOLD';
    }
}
</script>
</body>
</html>
