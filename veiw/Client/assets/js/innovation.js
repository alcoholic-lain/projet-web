console.log("🔥 innovation.js chargé avec succès");

// =============================
// CONFIG
// =============================
const API = "http://localhost/projet-web/controller/components/InnovationController.php";
const API_CAT = "http://localhost/projet-web/controller/components/CategoryController.php";


// =============================
// 0️⃣ MAP CATÉGORIES (ID → NOM)
// =============================
async function getCategoriesMap() {
    try {
        const res = await fetch(API_CAT);
        const data = await res.json();

        const map = {};
        data.records.forEach(cat => map[cat.id] = cat.nom);

        return map;
    } catch (err) {
        console.error("Erreur chargement catégories :", err);
        return {};
    }
}


// =============================
// 1️⃣ CHARGER LISTE DES CATÉGORIES (FORM ADD/EDIT)
// =============================
async function loadCategories() {
    const select = document.getElementById("category_id");
    if (!select) return;

    try {
        const res = await fetch(API_CAT);
        const data = await res.json();

        if (!data.success) return;

        data.records.forEach(cat => {
            const opt = document.createElement("option");
            opt.value = cat.id;
            opt.textContent = cat.nom;
            select.appendChild(opt);
        });

    } catch (err) {
        console.error("Erreur chargement catégories :", err);
    }
}


// =============================
// 2️⃣ AJOUTER UNE INNOVATION (VALIDATION + SHAKE)
// =============================
async function setupAddInnovationPage() {
    const form = document.getElementById("form-innovation");
    if (!form) return;

    const titreEl = document.getElementById("titre");
    const descriptionEl = document.getElementById("description");
    const categoryEl = document.getElementById("category_id");
    const msg = document.getElementById("msg");

    // Fonction shake
    function shake(el) {
        el.classList.remove("shake");
        void el.offsetWidth;
        el.classList.add("shake");
    }

    // Validation
    function validate() {
        msg.textContent = "";
        msg.style.color = "red";

        if (titreEl.value.trim().length < 3) {
            msg.textContent = "⚠️ Le titre doit comporter au moins 3 caractères.";
            shake(titreEl);
            return false;
        }

        if (descriptionEl.value.trim().length < 5) {
            msg.textContent = "⚠️ La description est trop courte.";
            shake(descriptionEl);
            return false;
        }

        if (!categoryEl.value) {
            msg.textContent = "⚠️ Veuillez choisir une catégorie.";
            shake(categoryEl);
            return false;
        }

        return true;
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        if (!validate()) return;

        const payload = {
            titre: titreEl.value.trim(),
            description: descriptionEl.value.trim(),
            category_id: categoryEl.value
        };

        try {
            const res = await fetch(API, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (data.success) {
                msg.textContent = "✅ Innovation ajoutée avec succès !";
                msg.style.color = "#4AFF8B";
                setTimeout(() => window.location.href = "list_Innovation.html", 1200);
            } else {
                msg.textContent = "❌ " + (data.message || "Erreur inconnue");
            }

        } catch (err) {
            msg.textContent = "❌ Erreur serveur";
        }
    });
}


// =============================
// 3️⃣ MODIFIER UNE INNOVATION
// =============================
async function setupEditInnovationPage() {
    const form = document.getElementById("form-edit-innovation");
    if (!form) return;

    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");

    const msg = document.getElementById("msg");
    const titreEl = document.getElementById("titre");
    const descEl = document.getElementById("description");
    const catSelect = document.getElementById("category_id");
    const statutEl = document.getElementById("statut");

    // Charger catégories
    try {
        const resCat = await fetch(API_CAT);
        const dataCat = await resCat.json();

        catSelect.innerHTML = "";
        dataCat.records.forEach(cat => {
            catSelect.innerHTML += `<option value="${cat.id}">${cat.nom}</option>`;
        });

    } catch (e) {
        console.error("Erreur catégories :", e);
    }

    // Charger innovation
    try {
        const res = await fetch(`${API}?id=${id}`);
        const inv = await res.json();

        titreEl.value = inv.titre;
        descEl.value = inv.description;
        statutEl.value = inv.statut ?? "En attente";
        catSelect.value = inv.category_id;

    } catch (e) {
        msg.textContent = "❌ Erreur chargement innovation";
        msg.style.color = "red";
        return;
    }

    // Soumission
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const payload = {
            id,
            titre: titreEl.value.trim(),
            description: descEl.value.trim(),
            category_id: catSelect.value,
            statut: statutEl.value
        };

        try {
            const res = await fetch(API, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (data.success) {
                msg.textContent = "✔ Innovation mise à jour avec succès";
                msg.style.color = "lightgreen";
                setTimeout(() => window.location.href = "a_Innovation.html", 1000);
            } else {
                msg.textContent = "❌ " + data.message;
                msg.style.color = "red";
            }

        } catch (error) {
            msg.textContent = "❌ Erreur serveur";
            msg.style.color = "red";
        }
    });
}


// =============================
// 4️⃣ LISTE DES INNOVATIONS (User)
// =============================
async function afficherListe() {
    const table = document.getElementById("innovation-list");
    if (!table) return;

    try {
        const catMap = await getCategoriesMap();
        const res = await fetch(API);
        const data = await res.json();

        table.innerHTML = `
            <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>`;

        data.records.forEach(inv => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td style="cursor:pointer;color:#6C63FF">${inv.titre}</td>
                <td>${catMap[inv.category_id] ?? "Inconnue"}</td>
                <td>${inv.date_creation}</td>
                <td>${inv.statut ?? "En attente"}</td>`;

            row.addEventListener("click", () => {
                window.location.href =
                    `/projet-web/veiw/Client/src/details_Innovation.html?id=${inv.id}&from=user`;
            });

            table.appendChild(row);
        });

    } catch (err) {
        console.error("Erreur chargement innovations :", err);
        table.innerHTML = "<tr><td colspan='4'>Erreur de chargement</td></tr>";
    }
}


// =============================
// 5️⃣ DETAILS INNOVATION
// =============================
async function afficherDetails() {
    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");
    if (!id) return;

    try {
        const res = await fetch(`${API}?id=${id}`);
        const inv = await res.json();

        if (!inv || inv.success === false) return;

        document.getElementById("titre").textContent = inv.titre;
        document.getElementById("desc").textContent = inv.description;
        document.getElementById("date").textContent = inv.date_creation;
        document.getElementById("statut").textContent = inv.statut ?? "En attente";
        window.currentCategoryId = inv.category_id;

    } catch (err) {
        console.error("Erreur chargement détails innovation :", err);
    }
}


// =============================
// 6️⃣ Retour intelligent
// =============================
function initRetour() {
    const btn = document.getElementById("btn-retour");
    if (!btn) return;

    const params = new URLSearchParams(window.location.search);
    const from = params.get("from") ?? "visitor";

    btn.addEventListener("click", () => {
        if (from === "admin")
            return window.location.href = "/projet-web/veiw/Admin/a_Innovation.html";

        if (from === "user")
            return window.location.href = "/projet-web/veiw/Client/src/list_Innovation.html";

        if (window.currentCategoryId)
            return window.location.href =
                `/projet-web/veiw/Client/src/category_details.html?id=${window.currentCategoryId}`;

        window.location.href = "/projet-web/veiw/Client/src/categories.html";
    });
}


// =============================
// 7️⃣ Admin – tableau des innovations
// =============================
async function afficherAdmin() {
    const table = document.getElementById("admin-table");
    if (!table) return;

    try {
        const catMap = await getCategoriesMap();
        const res = await fetch(API);
        const data = await res.json();

        table.innerHTML = "";

        data.records.forEach(inv => {
            const row = document.createElement("tr");

            row.id = `row-${inv.id}`;
            row.innerHTML = `
                <td onclick="ouvrirDetails(${inv.id})" style="cursor:pointer;color:#8A8DFF;">${inv.titre}</td>
                <td>${catMap[inv.category_id] ?? "Inconnue"}</td>
                <td>${inv.date_creation}</td>
                <td id="statut-${inv.id}">${inv.statut ?? "En attente"}</td>
                <td>
                    <button class="valider" onclick="validerInnovation(${inv.id})">Valider</button>
                    <button class="rejeter" onclick="rejeterInnovation(${inv.id})">Rejeter</button>
                    <button class="btn-edit" onclick="modifierInnovation(${inv.id})">Modifier</button>
                    <button class="delete" onclick="deleteInnovation(${inv.id})">Supprimer</button>
                </td>`;

            table.appendChild(row);
        });

    } catch (err) {
        console.error("Erreur admin :", err);
    }
}


// =============================
// 8️⃣ Actions Admin
// =============================
function ouvrirDetails(id) {
    window.location.href =
        `/projet-web/veiw/Client/src/details_Innovation.html?id=${id}&from=admin`;
}

async function updateStatut(id, statut) {
    try {
        const res = await fetch(API, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id, statut })
        });

        const data = await res.json();
        if (data.success)
            document.getElementById(`statut-${id}`).textContent = statut;

    } catch (err) {
        console.error("Erreur update statut :", err);
    }
}

function validerInnovation(id) { updateStatut(id, "Validée"); }
function rejeterInnovation(id) { updateStatut(id, "Rejetée"); }

async function deleteInnovation(id) {
    if (!confirm("Voulez-vous supprimer cette innovation ?")) return;

    try {
        const res = await fetch(API, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        });

        const data = await res.json();

        if (data.success)
            document.getElementById(`row-${id}`).remove();

    } catch (err) {
        console.error("Erreur suppression :", err);
    }
}

function modifierInnovation(id) {
    window.location.href = `edit_Innovation.html?id=${id}`;
}


// =============================
// 🔟 AUTO-INIT
// =============================
document.addEventListener("DOMContentLoaded", () => {
    const path = location.pathname;

    if (path.includes("add_Innovation.html")) {
        loadCategories();
        setupAddInnovationPage();
    }

    if (path.includes("list_Innovation.html")) afficherListe();
    if (path.includes("details_Innovation.html")) { afficherDetails(); initRetour(); }
    if (path.includes("a_Innovation.html")) afficherAdmin();
    if (path.includes("edit_Innovation.html")) setupEditInnovationPage();
});
