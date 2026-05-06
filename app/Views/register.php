<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> — RégimeSport</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="logo-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </div>
            <h1>Créer un compte</h1>
            <p>Rejoignez notre communauté santé</p>
        </div>

        <div class="auth-body">
            <!-- Indicateur d'étapes -->
            <div class="steps-indicator mb-24">
                <div class="step-dot active" id="dot1">1</div>
                <div class="step-line" id="line1"></div>
                <div class="step-dot" id="dot2">2</div>
            </div>

            <!-- Alertes -->
            <div id="alert-box" style="display:none"></div>

            <!-- ÉTAPE 1 : Informations de base -->
            <div id="step1">
                <h3 class="mb-16" style="font-size:1rem;color:var(--ink-soft)">Vos informations</h3>

                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <div class="input-wrap">
                        <span class="input-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                        <input class="form-control" type="text" id="nom" placeholder="Jean Dupont" required>
                    </div>
                    <span class="form-error" id="err-nom"></span>
                </div>

                <div class="form-group">
                    <label class="form-label">Adresse e-mail</label>
                    <div class="input-wrap">
                        <span class="input-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></span>
                        <input class="form-control" type="email" id="email" placeholder="jean@exemple.mg" required>
                    </div>
                    <span class="form-error" id="err-email"></span>
                </div>

                <div class="form-group">
                    <label class="form-label">Genre</label>
                    <select class="form-control" id="genre" required>
                        <option value="">Sélectionner...</option>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                    <span class="form-error" id="err-genre"></span>
                </div>

                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <div class="input-wrap">
                        <span class="input-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                        <input class="form-control" type="password" id="password" placeholder="Min. 6 caractères" required>
                    </div>
                    <span class="form-error" id="err-password"></span>
                </div>

                <button class="btn btn-primary btn-full btn-lg" id="btn-step1" onclick="submitStep1()">
                    Continuer →
                </button>
            </div>

            <!-- ÉTAPE 2 : Données de santé (masquée initialement) -->
            <div id="step2" style="display:none">
                <h3 class="mb-8" style="font-size:1rem;color:var(--ink-soft)">Vos données de santé</h3>
                <p class="mb-16" style="font-size:.8rem">Ces informations nous permettent de calculer votre IMC et personnaliser vos programmes.</p>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Taille (en mètres)</label>
                        <input class="form-control" type="number" id="taille" placeholder="1.75" step="0.01" min="0.5" max="3" required>
                        <span class="form-error" id="err-taille"></span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Poids (en kg)</label>
                        <input class="form-control" type="number" id="poids" placeholder="70" step="0.1" min="20" max="300" required>
                        <span class="form-error" id="err-poids"></span>
                    </div>
                </div>

                <!-- Aperçu IMC en temps réel -->
                <div id="imc-preview" class="imc-card mb-16" style="display:none;padding:20px">
                    <div style="font-size:.75rem;opacity:.7;text-transform:uppercase;letter-spacing:.05em">Votre IMC estimé</div>
                    <div class="imc-value" id="imc-val" style="font-size:2.5rem;margin-top:4px">—</div>
                    <div class="imc-category" id="imc-cat"></div>
                </div>

                <div class="d-flex gap-12">
                    <button class="btn btn-outline" onclick="backToStep1()">← Retour</button>
                    <button class="btn btn-primary flex-1 btn-lg" id="btn-step2" onclick="submitStep2()">
                        Créer mon compte
                    </button>
                </div>
            </div>
        </div>

        <div class="auth-footer">
            Déjà un compte ? <a href="<?= base_url('/login') ?>">Se connecter →</a>
        </div>
    </div>
</div>

<script>
const CSRF_TOKEN = '<?= csrf_hash() ?>';
const CSRF_NAME  = '<?= csrf_token() ?>';

function showAlert(message, type = 'error') {
    const box = document.getElementById('alert-box');
    box.style.display = 'flex';
    box.className = `alert alert-${type}`;
    box.innerHTML = message;
}

function clearErrors() {
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
}

async function submitStep1() {
    clearErrors();
    const btn = document.getElementById('btn-step1');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Vérification...';

    const data = new FormData();
    data.append(CSRF_NAME, CSRF_TOKEN);
    data.append('nom',      document.getElementById('nom').value);
    data.append('email',    document.getElementById('email').value);
    data.append('genre',    document.getElementById('genre').value);
    data.append('password', document.getElementById('password').value);

    try {
        const res  = await fetch('<?= site_url("/register/step1") ?>', { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const json = await res.json();

        if (json.success) {
            // Passe à l'étape 2
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            document.getElementById('dot1').classList.replace('active', 'done');
            document.getElementById('dot1').textContent = '✓';
            document.getElementById('line1').classList.add('done');
            document.getElementById('dot2').classList.add('active');
            document.getElementById('alert-box').style.display = 'none';
        } else if (json.errors) {
            Object.entries(json.errors).forEach(([field, msg]) => {
                const errEl = document.getElementById('err-' + field);
                const input = document.getElementById(field);
                if (errEl) errEl.textContent = msg;
                if (input) input.classList.add('is-invalid');
            });
        }
    } catch(e) {
        showAlert('Erreur réseau. Réessayez.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Continuer →';
    }
}

function backToStep1() {
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = 'block';
    document.getElementById('dot2').classList.remove('active');
    document.getElementById('dot1').className = 'step-dot active';
    document.getElementById('dot1').textContent = '1';
    document.getElementById('line1').classList.remove('done');
}

async function submitStep2() {
    clearErrors();
    const btn = document.getElementById('btn-step2');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Création...';

    const data = new FormData();
    data.append(CSRF_NAME, CSRF_TOKEN);
    data.append('taille', document.getElementById('taille').value);
    data.append('poids',  document.getElementById('poids').value);

    try {
        const res  = await fetch('<?= site_url("/register/step2") ?>', { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const json = await res.json();

        if (json.success) {
            showAlert('✅ Compte créé ! Redirection...', 'success');
            setTimeout(() => window.location.href = json.redirect, 1200);
        } else if (json.errors) {
            Object.entries(json.errors).forEach(([field, msg]) => {
                const errEl = document.getElementById('err-' + field);
                if (errEl) errEl.textContent = msg;
            });
        } else {
            showAlert(json.message || 'Erreur lors de la création.');
        }
    } catch(e) {
        showAlert('Erreur réseau. Réessayez.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Créer mon compte';
    }
}

// Aperçu IMC en temps réel
function updateIMCPreview() {
    const taille = parseFloat(document.getElementById('taille').value);
    const poids  = parseFloat(document.getElementById('poids').value);
    const preview= document.getElementById('imc-preview');

    if (taille > 0.5 && poids > 20) {
        const imc = (poids / (taille * taille)).toFixed(1);
        let cat = '';
        if (imc < 18.5)      cat = 'Insuffisance pondérale';
        else if (imc < 25)   cat = 'Corpulence normale ✓';
        else if (imc < 30)   cat = 'Surpoids';
        else                 cat = 'Obésité';

        document.getElementById('imc-val').textContent = imc;
        document.getElementById('imc-cat').textContent = cat;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

document.getElementById('taille').addEventListener('input', updateIMCPreview);
document.getElementById('poids').addEventListener('input', updateIMCPreview);
</script>
</body>
</html>
