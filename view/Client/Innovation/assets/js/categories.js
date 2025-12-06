/* ===========================
   GALAXY 3D PARALLAX
   =========================== */

function initGalaxy() {
    const existing = document.getElementById('galaxyCanvas');
    if (existing) return;

    const canvas = document.createElement('canvas');
    canvas.id = 'galaxyCanvas';
    document.body.appendChild(canvas);

    const ctx = canvas.getContext('2d');
    let width = window.innerWidth;
    let height = window.innerHeight;
    canvas.width = width;
    canvas.height = height;

    const STAR_COUNT = 260;
    const stars = [];
    const colors = ['#ff7f2a', '#ff9d5c', '#ff2a7f', '#ffd0a1'];

    for (let i = 0; i < STAR_COUNT; i++) {
        stars.push({
            x: Math.random() * width,
            y: Math.random() * height,
            z: Math.random() * 1.2 + 0.2,
            radius: Math.random() * 1.6 + 0.4,
            speed: Math.random() * 0.35 + 0.05,
            color: colors[Math.floor(Math.random() * colors.length)]
        });
    }

    let mouseX = 0;
    let mouseY = 0;
    let targetMouseX = 0;
    let targetMouseY = 0;

    window.addEventListener('mousemove', (e) => {
        const centerX = width / 2;
        const centerY = height / 2;
        targetMouseX = (e.clientX - centerX) / centerX;
        targetMouseY = (e.clientY - centerY) / centerY;
    });

    window.addEventListener('resize', () => {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
    });

    function render() {
        mouseX += (targetMouseX - mouseX) * 0.06;
        mouseY += (targetMouseY - mouseY) * 0.06;

        ctx.fillStyle = 'rgba(5, 8, 20, 0.78)';
        ctx.fillRect(0, 0, width, height);

        for (const s of stars) {
            s.y += s.speed * (1.2 - s.z);
            if (s.y > height + 20) {
                s.y = -20;
                s.x = Math.random() * width;
                s.z = Math.random() * 1.2 + 0.2;
            }

            const parallaxX = mouseX * (30 * s.z);
            const parallaxY = mouseY * (18 * s.z);

            const drawX = s.x + parallaxX;
            const drawY = s.y + parallaxY;

            const size = s.radius * (1.5 - s.z);

            const gradient = ctx.createRadialGradient(
                drawX, drawY, 0,
                drawX, drawY, size * 3
            );
            gradient.addColorStop(0, s.color);
            gradient.addColorStop(0.45, s.color);
            gradient.addColorStop(1, 'rgba(0,0,0,0)');

            ctx.fillStyle = gradient;
            ctx.beginPath();
            ctx.arc(drawX, drawY, size * 2.3, 0, Math.PI * 2);
            ctx.fill();
        }

        requestAnimationFrame(render);
    }

    render();
}

/* ===========================
   SEARCH + GRID/LIST TOGGLE
   =========================== */

function setupCategoriesUI() {
    const searchInput = document.getElementById('search-input');
    const grid = document.getElementById('categories-grid');
    const list = document.getElementById('categories-list');
    const empty = document.getElementById('empty-state');
    const btnGrid = document.getElementById('grid-view');
    const btnList = document.getElementById('list-view');

    if (!grid || !list) return;

    function filterCategories() {
        const term = (searchInput.value || '').toLowerCase().trim();
        let anyVisible = false;

        // grid cards
        const gridItems = grid.querySelectorAll('[data-name]');
        gridItems.forEach(el => {
            const name = el.getAttribute('data-name') || '';
            const visible = !term || name.includes(term);
            el.style.display = visible ? '' : 'none';
            if (visible) anyVisible = true;
        });

        // list rows
        const listItems = list.querySelectorAll('[data-name]');
        listItems.forEach(el => {
            const name = el.getAttribute('data-name') || '';
            const visible = !term || name.includes(term);
            el.style.display = visible ? '' : 'none';
        });

        if (!anyVisible) {
            empty.classList.remove('hidden');
        } else {
            empty.classList.add('hidden');
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterCategories);
    }

    function setGridView() {
        grid.classList.remove('hidden');
        list.classList.add('hidden');
        btnGrid.classList.add('cs-toggle-active');
        btnList.classList.remove('cs-toggle-active');
        filterCategories();
    }

    function setListView() {
        grid.classList.add('hidden');
        list.classList.remove('hidden');
        btnList.classList.add('cs-toggle-active');
        btnGrid.classList.remove('cs-toggle-active');
        filterCategories();
    }

    if (btnGrid) btnGrid.addEventListener('click', setGridView);
    if (btnList) btnList.addEventListener('click', setListView);

    // Vue par défaut : grid
    setGridView();
}

/* ===========================
   INIT
   =========================== */

document.addEventListener('DOMContentLoaded', () => {
    initGalaxy();
    setupCategoriesUI();
});
document.getElementById("myProfileBtn").addEventListener("click", () => {
    window.location.href = "/projet-web/view/Client/login/profile.php";
});
// --- IA SEARCH FINAL CLEAN VERSION ---
/* ===========================
   IA SEARCH CLEAN VERSION
   =========================== */

const chat = document.getElementById("aiChat");
const input = document.getElementById("aiInput");
const send = document.getElementById("aiSend");

// --- ENVOI ---
send.addEventListener("click", () => askAI(input.value.trim()));
input.addEventListener("keypress", e => {
    if (e.key === "Enter") askAI(input.value.trim());
});

// --- AFFICHAGE MESSAGES ---
function addMessage(type, text) {
    const div = document.createElement("div");
    div.className = type === "user" ? "msg-user" : "msg-bot";
    div.innerText = text;
    chat.appendChild(div);
    chat.scrollTop = chat.scrollHeight;
    return div;
}

function removeMessage(el) {
    if (el && el.parentNode) el.parentNode.removeChild(el);
}

// --- REQUÊTE IA ---
async function askAI(query) {

    if (!query || query.length < 2) return;

    addMessage("user", query);
    input.value = "";

    // Message de chargement
    const loading = addMessage("bot", "✨ Analyse des données… 🚀");

    try {
        const url = `/projet-web/controller/API/client/ai_search.php?q=${encodeURIComponent(query)}`;
        const response = await fetch(url);
        const raw = await response.text();

        let data;
        try {
            data = JSON.parse(raw);
        } catch (e) {
            removeMessage(loading);
            addMessage("bot", "❌ Erreur : réponse invalide de l'IA.");
            console.error("RAW RESPONSE:", raw);
            return;
        }

        removeMessage(loading);

        if (!data.success) {
            addMessage("bot", "❌ Erreur : " + (data.message || "Impossible de traiter la requête."));
            return;
        }

        addMessage("bot", `Voici les résultats pour « ${query} » 👇`);
        displayAIResults(data);

    } catch (error) {
        removeMessage(loading);
        addMessage("bot", "❌ Erreur de connexion à l'IA.");
        console.error(error);
    }
}


/* ===========================
   AFFICHAGE DES RÉSULTATS IA
   =========================== */

function displayAIResults(data) {

    const cats = data.categories;
    const inn = data.innovations;

    const box = document.getElementById("aiResultsBox");      // Conteneur global
    const catBox = document.getElementById("aiCategories");    // Catégories
    const innBox = document.getElementById("aiInnovations");   // Innovations
    const empty = document.getElementById("aiNoResults");      // Aucun résultat

    // Reset
    box.classList.remove("hidden");
    catBox.innerHTML = "";
    innBox.innerHTML = "";
    empty.classList.add("hidden");

    let found = false;

    /* === Catégories trouvées === */
    if (cats.length > 0) {
        found = true;
        cats.forEach(c => {
            catBox.innerHTML += `
                <div class="result-card">
                    <div class="result-card-title">${c.nom}</div>
                    <div class="result-card-desc">${c.description}</div>
                    <a class="cs-btn-link" href="list_Innovation.php?categorie=${c.id}">
                        Voir les innovations →
                    </a>
                </div>`;
        });
    }
    /* === Innovations === */
    if (inn.length > 0) {
        found = true;
        inn.forEach(i => {
            innBox.innerHTML += `
            <div class="result-card">
                <div class="result-card-title">${i.titre}</div>
                <div class="result-card-desc">${i.description}</div>
                <div class="ai-cat">Catégorie : ${i.categorie}</div>

                <a class="cs-btn-link"
                   href="/projet-web/view/Client/Innovation/src/details_Innovation.php?id=${i.id}">
                    Ouvrir →
                </a>
            </div>`;
        });
    }


    /* === Aucun résultat === */
    if (!found) {
        empty.classList.remove("hidden");
    }
}

