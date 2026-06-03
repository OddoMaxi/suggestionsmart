<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#6366f1">
    <title>Votre avis — SmartSuggest</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Inter', sans-serif; overscroll-behavior: none; }

        /* Slide animations */
        .step { display: none; animation: slideIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .step.active { display: block; }
        .step.exit { animation: slideOut 0.25s ease forwards; }
        @keyframes slideIn  { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
        @keyframes slideOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(-40px); } }

        /* Type cards */
        .type-card { transition: all 0.2s cubic-bezier(0.34,1.56,0.64,1); cursor:pointer; }
        .type-card:active { transform: scale(0.94); }
        .type-card.selected { transform: scale(1.03); }

        /* Service card */
        .service-card { transition: all 0.15s ease; cursor:pointer; }
        .service-card:active { transform: scale(0.97); }
        .service-card.selected { border-color: #6366f1 !important; background: #eef2ff !important; }

        /* Input style */
        .input-field {
            width:100%; padding:14px 16px; border:2px solid #e5e7eb;
            border-radius:14px; font-size:16px; background:#fafafa;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline:none; appearance:none;
        }
        .input-field:focus { border-color:#6366f1; background:#fff; box-shadow:0 0 0 4px rgba(99,102,241,0.1); }

        /* Stars */
        .star-btn { font-size:2.2rem; transition:transform 0.15s ease; cursor:pointer; line-height:1; }
        .star-btn:active { transform: scale(0.85); }
        .star-btn.lit { filter: drop-shadow(0 0 6px #f59e0b); }

        /* Textarea */
        textarea.input-field { resize:none; min-height:140px; }

        /* Submit button pulse */
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(99,102,241,0.5); }
            70%  { box-shadow: 0 0 0 14px rgba(99,102,241,0); }
            100% { box-shadow: 0 0 0 0 rgba(99,102,241,0); }
        }
        .btn-submit { animation: pulse-ring 2s ease infinite; }
        .btn-submit:active { transform:scale(0.97); }

        /* Progress bar */
        .progress-fill { transition: width 0.4s cubic-bezier(0.4,0,0.2,1); }

        /* Floating back button */
        .back-btn { transition: background 0.15s; }
        .back-btn:active { background: #e5e7eb !important; }

        /* Character counter */
        .char-ok   { color: #22c55e; }
        .char-warn { color: #f59e0b; }
        .char-err  { color: #ef4444; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

{{-- Header fixe --}}
<div class="sticky top-0 z-20 bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-md mx-auto px-4 py-3">
        <div class="flex items-center justify-between mb-2.5">
            <button id="backBtn" onclick="prevStep()"
                    class="back-btn hidden w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div id="stepLabel" class="text-sm font-semibold text-gray-500 mx-auto">Étape 1 sur 4</div>
            <div class="w-9"></div>
        </div>
        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div id="progressBar" class="progress-fill h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full" style="width:25%"></div>
        </div>
    </div>
</div>

<form id="mainForm" action="{{ route('suggestion.enregistrer') }}" method="POST">
@csrf
<input type="hidden" name="service_id"   id="f_service_id">
<input type="hidden" name="type"         id="f_type">
<input type="hidden" name="nom"          id="f_nom">
<input type="hidden" name="prenom"       id="f_prenom">
<input type="hidden" name="telephone"    id="f_telephone">
<input type="hidden" name="email"        id="f_email">
<input type="hidden" name="message"      id="f_message">
<input type="hidden" name="satisfaction" id="f_satisfaction">

<div class="max-w-md mx-auto px-4 pb-24 pt-6">

    {{-- ══════════════ ÉTAPE 1 — TYPE ══════════════ --}}
    <div id="step1" class="step active">
        <div class="text-center mb-8">
            <div class="text-4xl mb-3">👋</div>
            <h1 class="text-2xl font-bold text-gray-900">Bonjour !</h1>
            <p class="text-gray-500 mt-1 text-sm">Que souhaitez-vous partager ?</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            @foreach([
                ['suggestion',   '💡', 'Suggestion',   'Partagez une idée', 'from-blue-400 to-blue-600',   'bg-blue-50 border-blue-200'],
                ['felicitation', '🌟', 'Félicitation',  'Exprimez votre satisfaction', 'from-green-400 to-emerald-600', 'bg-green-50 border-green-200'],
                ['critique',     '⚠️', 'Critique',      'Signalez un problème', 'from-amber-400 to-orange-500', 'bg-amber-50 border-amber-200'],
                ['reclamation',  '🚨', 'Réclamation',   'Demandez une action', 'from-red-400 to-rose-600',   'bg-red-50 border-red-200'],
            ] as [$val, $emoji, $label, $desc, $grad, $bg])
            <div class="type-card border-2 {{ $bg }} rounded-2xl p-4 text-center"
                 data-type="{{ $val }}"
                 onclick="selectType('{{ $val }}', this)">
                <div class="text-3xl mb-2">{{ $emoji }}</div>
                <div class="font-bold text-gray-800 text-sm">{{ $label }}</div>
                <div class="text-gray-500 text-xs mt-0.5 leading-tight">{{ $desc }}</div>
            </div>
            @endforeach
        </div>

        @if($serviceSelectionne)
        <input type="hidden" id="preselected_service" value="{{ $serviceSelectionne->id }}">
        @endif
    </div>

    {{-- ══════════════ ÉTAPE 2 — SERVICE ══════════════ --}}
    <div id="step2" class="step">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900">Quel service ?</h2>
            <p class="text-gray-500 text-sm mt-1">Sélectionnez le service concerné</p>
        </div>

        <div id="serviceSearch" class="mb-3">
            <input type="text" placeholder="🔍 Rechercher un service…"
                   class="input-field text-sm" oninput="filterServices(this.value)">
        </div>

        <div id="serviceList" class="space-y-2">
            @foreach($services as $service)
            <div class="service-card border-2 border-gray-100 bg-white rounded-2xl px-4 py-3.5 flex items-center gap-3"
                 data-id="{{ $service->id }}"
                 data-name="{{ strtolower($service->nom . ' ' . optional($service->agence)->nom) }}"
                 onclick="selectService('{{ $service->id }}', '{{ addslashes($service->nom) }}', this)">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:{{ $service->couleur }}22; color:{{ $service->couleur }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-gray-800 text-sm truncate">{{ $service->nom }}</div>
                    @if($service->agence)
                    <div class="text-xs text-gray-400 truncate">{{ $service->agence->nom }}</div>
                    @endif
                </div>
                <div class="check-icon hidden text-indigo-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                </div>
            </div>
            @endforeach
        </div>

        <div id="noService" class="hidden text-center py-8 text-gray-400 text-sm">
            Aucun service trouvé
        </div>
    </div>

    {{-- ══════════════ ÉTAPE 3 — IDENTITÉ ══════════════ --}}
    <div id="step3" class="step">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900">Qui êtes-vous ?</h2>
            <p class="text-gray-500 text-sm mt-1">Vos coordonnées restent confidentielles</p>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prénom <span class="text-indigo-500">*</span></label>
                <input id="inp_prenom" type="text" inputmode="text" autocomplete="given-name"
                       class="input-field" placeholder="Votre prénom"
                       oninput="validateStep3()">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom <span class="text-indigo-500">*</span></label>
                <input id="inp_nom" type="text" inputmode="text" autocomplete="family-name"
                       class="input-field" placeholder="Votre nom de famille"
                       oninput="validateStep3()">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Téléphone <span class="text-indigo-500">*</span></label>
                <input id="inp_tel" type="tel" inputmode="tel" autocomplete="tel"
                       class="input-field" placeholder="+224 6XX XXX XXX"
                       oninput="validateStep3()">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Email <span class="text-gray-400 font-normal text-xs">(facultatif)</span>
                </label>
                <input id="inp_email" type="email" inputmode="email" autocomplete="email"
                       class="input-field" placeholder="votre@email.com">
            </div>
        </div>

        <div id="step3Error" class="hidden mt-4 bg-red-50 border border-red-200 rounded-xl p-3 text-red-600 text-sm text-center"></div>

        <button type="button" id="step3Btn" onclick="goToStep4()"
                class="mt-6 w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl text-base disabled:opacity-40 disabled:cursor-not-allowed transition-opacity"
                disabled>
            Continuer →
        </button>
    </div>

    {{-- ══════════════ ÉTAPE 4 — MESSAGE ══════════════ --}}
    <div id="step4" class="step">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900">Votre message</h2>
            <p class="text-gray-500 text-sm mt-1">Décrivez en détail votre <span id="typeLabel" class="font-semibold text-indigo-600">message</span></p>
        </div>

        {{-- Note satisfaction --}}
        <div class="bg-white rounded-2xl border-2 border-gray-100 p-4 mb-4">
            <p class="text-sm font-semibold text-gray-700 mb-3 text-center">Votre satisfaction globale <span class="text-gray-400 font-normal">(optionnel)</span></p>
            <div class="flex justify-center gap-2" id="starRow">
                @for($i = 1; $i <= 5; $i++)
                <button type="button" class="star-btn text-gray-200" data-star="{{ $i }}" onclick="setStar({{ $i }})">★</button>
                @endfor
            </div>
            <p id="starLabel" class="text-center text-xs text-gray-400 mt-2 h-4"></p>
        </div>

        {{-- Textarea --}}
        <div class="relative">
            <textarea id="inp_message" class="input-field pr-4"
                      placeholder="Décrivez votre message ici… (minimum 10 caractères)"
                      maxlength="3000"
                      oninput="updateCharCount(); validateStep4()"></textarea>
            <div id="charCount" class="text-xs text-gray-400 text-right mt-1">0 / 3000</div>
        </div>

        <div id="step4Error" class="hidden mt-3 bg-red-50 border border-red-200 rounded-xl p-3 text-red-600 text-sm text-center"></div>

        {{-- Récap --}}
        <div id="recap" class="mt-4 bg-indigo-50 border border-indigo-100 rounded-2xl p-4 space-y-2">
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500">Type</span>
                <span id="recap_type" class="font-semibold text-gray-800"></span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500">Service</span>
                <span id="recap_service" class="font-semibold text-gray-800 text-right max-w-[180px] truncate"></span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500">Auteur</span>
                <span id="recap_auteur" class="font-semibold text-gray-800"></span>
            </div>
        </div>

        <button type="button" id="submitBtn" onclick="soumettre()"
                class="btn-submit mt-6 w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 rounded-2xl text-base flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                disabled>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            Envoyer mon message
        </button>

        <p class="text-center text-xs text-gray-400 mt-3">🔒 Données confidentielles</p>
    </div>

</div>
</form>

{{-- Overlay chargement --}}
<div id="loadingOverlay" class="hidden fixed inset-0 bg-indigo-900/80 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="text-center text-white">
        <div class="w-14 h-14 border-4 border-white/30 border-t-white rounded-full animate-spin mx-auto mb-4"></div>
        <p class="font-semibold text-lg">Envoi en cours…</p>
    </div>
</div>

<script>
// ─── État global ───────────────────────────────────────────────
let currentStep = 1;
let selectedType = null;
let selectedServiceId = null;
let selectedServiceName = null;
let selectedStar = null;
const totalSteps = 4;

const stepLabels = {
    1: 'Étape 1 sur 4',
    2: 'Étape 2 sur 4',
    3: 'Étape 3 sur 4',
    4: 'Étape 4 sur 4',
};
const typeLabels = {
    suggestion: '💡 Suggestion',
    felicitation: '🌟 Félicitation',
    critique: '⚠️ Critique',
    reclamation: '🚨 Réclamation',
};
const starLabels = ['', 'Très insatisfait 😞', 'Insatisfait 😕', 'Neutre 😐', 'Satisfait 😊', 'Très satisfait 😍'];

// ─── Navigation ─────────────────────────────────────────────────
function goToStep(n) {
    const prev = document.getElementById('step' + currentStep);
    prev.classList.remove('active');

    currentStep = n;
    const next = document.getElementById('step' + currentStep);
    next.classList.add('active');

    document.getElementById('progressBar').style.width = (currentStep / totalSteps * 100) + '%';
    document.getElementById('stepLabel').textContent = stepLabels[currentStep];
    document.getElementById('backBtn').classList.toggle('hidden', currentStep === 1);

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep() {
    if (currentStep > 1) goToStep(currentStep - 1);
}

// ─── Étape 1 : sélection du type ────────────────────────────────
function selectType(type, el) {
    selectedType = type;
    document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected', 'ring-4', 'ring-indigo-300'));
    el.classList.add('selected', 'ring-4', 'ring-indigo-300');

    // Check si service pré-sélectionné
    const preselect = document.getElementById('preselected_service');
    setTimeout(() => {
        if (preselect) {
            selectedServiceId = preselect.value;
            const card = document.querySelector(`[data-id="${selectedServiceId}"]`);
            if (card) {
                selectedServiceName = card.querySelector('.font-semibold').textContent;
                card.classList.add('selected');
                card.querySelector('.check-icon').classList.remove('hidden');
            }
            goToStep(3);
        } else {
            goToStep(2);
        }
    }, 300);
}

// ─── Étape 2 : sélection du service ─────────────────────────────
function selectService(id, name, el) {
    selectedServiceId = id;
    selectedServiceName = name;

    document.querySelectorAll('.service-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('.check-icon').classList.add('hidden');
    });
    el.classList.add('selected');
    el.querySelector('.check-icon').classList.remove('hidden');

    setTimeout(() => goToStep(3), 250);
}

function filterServices(val) {
    const q = val.toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('.service-card').forEach(c => {
        const match = !q || c.dataset.name.includes(q);
        c.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('noService').classList.toggle('hidden', visible > 0);
}

// ─── Étape 3 : identité ─────────────────────────────────────────
function validateStep3() {
    const prenom = document.getElementById('inp_prenom').value.trim();
    const nom    = document.getElementById('inp_nom').value.trim();
    const tel    = document.getElementById('inp_tel').value.trim();
    const ok = prenom.length >= 2 && nom.length >= 2 && tel.length >= 6;
    document.getElementById('step3Btn').disabled = !ok;
}

function goToStep4() {
    const prenom = document.getElementById('inp_prenom').value.trim();
    const nom    = document.getElementById('inp_nom').value.trim();
    const tel    = document.getElementById('inp_tel').value.trim();

    if (!prenom || !nom || !tel) {
        showError('step3Error', 'Veuillez remplir tous les champs obligatoires.');
        return;
    }

    // Pré-remplir le récap
    document.getElementById('recap_type').textContent    = typeLabels[selectedType] || selectedType;
    document.getElementById('recap_service').textContent = selectedServiceName;
    document.getElementById('recap_auteur').textContent  = prenom + ' ' + nom;
    document.getElementById('typeLabel').textContent     = selectedType;

    hideError('step3Error');
    goToStep(4);
}

// ─── Étape 4 : message ──────────────────────────────────────────
function updateCharCount() {
    const msg = document.getElementById('inp_message');
    const n = msg.value.length;
    const el = document.getElementById('charCount');
    el.textContent = n + ' / 3000';
    el.className = 'text-xs text-right mt-1 ' + (n < 10 ? 'char-err' : n > 2800 ? 'char-warn' : 'char-ok');
}

function validateStep4() {
    const msg = document.getElementById('inp_message').value.trim();
    document.getElementById('submitBtn').disabled = msg.length < 10;
}

function setStar(n) {
    selectedStar = n;
    document.querySelectorAll('.star-btn').forEach((btn, i) => {
        const lit = i < n;
        btn.style.color = lit ? '#f59e0b' : '#e5e7eb';
        btn.classList.toggle('lit', lit);
        btn.style.transform = lit ? 'scale(1.15)' : '';
    });
    document.getElementById('starLabel').textContent = starLabels[n];
    document.getElementById('starLabel').style.opacity = 1;
}

// ─── Soumission ─────────────────────────────────────────────────
function soumettre() {
    const msg = document.getElementById('inp_message').value.trim();
    if (msg.length < 10) {
        showError('step4Error', 'Votre message doit contenir au moins 10 caractères.');
        return;
    }

    // Remplir les champs cachés
    document.getElementById('f_service_id').value  = selectedServiceId;
    document.getElementById('f_type').value         = selectedType;
    document.getElementById('f_prenom').value       = document.getElementById('inp_prenom').value.trim();
    document.getElementById('f_nom').value          = document.getElementById('inp_nom').value.trim();
    document.getElementById('f_telephone').value    = document.getElementById('inp_tel').value.trim();
    document.getElementById('f_email').value        = document.getElementById('inp_email').value.trim();
    document.getElementById('f_message').value      = msg;
    document.getElementById('f_satisfaction').value = selectedStar || '';

    document.getElementById('loadingOverlay').classList.remove('hidden');
    document.getElementById('mainForm').submit();
}

// ─── Utilitaires ─────────────────────────────────────────────────
function showError(id, msg) {
    const el = document.getElementById(id);
    el.textContent = msg;
    el.classList.remove('hidden');
}
function hideError(id) {
    document.getElementById(id).classList.add('hidden');
}

// ─── Init : pré-sélection via URL ────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    @if($serviceSelectionne)
    // Auto-sélectionner le service dans la liste (étape 2)
    const card = document.querySelector('[data-id="{{ $serviceSelectionne->id }}"]');
    if (card) {
        card.classList.add('selected');
        card.querySelector('.check-icon').classList.remove('hidden');
    }
    @endif
});
</script>

</body>
</html>
