console.log("🔥 innovation.js chargé avec succès");

// =============================
// CONFIG
// =============================
const API = "/controller/components/InnovationController.php";
const API_CAT = "/controller/components/CategoryController.php";


// =============================
// 0️⃣ MAP CATÉGORIES (ID → NOM)
// =============================
async function getCategoriesMap() {
    try {
        const res = await fetch(API_CAT);
        const data = await res.json();

        const map = {};
        data.records.forEach(cat => {
            map[cat.id] = cat.nom;
        });

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
// 2️⃣ AJOUTER UNE INNOVATION
// =============================
async function setupAddInnovationPage() {
    const form = document.getElementById("form-innovation");
    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const titre = document.getElementById("titre").value.trim();
        const description = document.getElementById("description").value.trim();
        const category_id = document.getElementById("category_id").value;
        const msg = document.getElementById("msg");

        if (!titre || !description || !category_id) {
            msg.textContent = "⚠️ Tous les champs sont obligatoires.";
            msg.style.color = "red";
            return;
        }

        const payload = { titre, description, category_id };

        try {
            const res = await fetch(API, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (data.success) {
                msg.textContent = "✅ Innovation ajoutée avec succès !";
                msg.style.color = "lightgreen";

                setTimeout(() => {
                    window.location.href = "list_Innovation.html";
                }, 1500);
            } else {
                msg.textContent = "❌ " + data.message;
                msg.style.color = "red";
            }

        } catch (err) {
            console.error("Erreur ajout innovation :", err);
            msg.textContent = "❌ Erreur serveur";
            msg.style.color = "red";
        }
    });
}
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

    // 1️⃣ Charger catégories
    try {
        const resCat = await fetch(API_CAT);
        const dataCat = await resCat.json();

        catSelect.innerHTML = ""; // éviter doublons

        dataCat.records.forEach(cat => {
            catSelect.innerHTML += `
                <option value="${cat.id}">${cat.nom}</option>
            `;
        });

    } catch (e) {
        console.error("Erreur categories :", e);
    }

    // 2️⃣ Charger innovation
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

    // 3️⃣ Soumission sans validation obligatoire
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
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (data.success) {
                msg.textContent = "✔ Innovation mise à jour avec succès";
                msg.style.color = "lightgreen";
                setTimeout(() => {
                    window.location.href = "a_Innovation.html";
                }, 1000);
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
// 3️⃣ LISTE DES INNOVATIONS (USER)
// =============================
async function afficherListe() {
    const table = document.getElementById("innovation-list");
    if (!table) return;

    try {
        // Charger catégorie → nom
        const catMap = await getCategoriesMap();

        // Charger innovations
        const res = await fetch(API);
        const data = await res.json();

        table.innerHTML = `
            <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>
        `;

        data.records.forEach(inv => {
            const row = document.createElement("tr");
            const categoryName = catMap[inv.category_id] ?? "Inconnue";

            row.innerHTML = `
                <td style="cursor:pointer;color:#6C63FF">${inv.titre}</td>
                <td>${categoryName}</td>
                <td>${inv.date_creation}</td>
                <td>${inv.statut ?? "En attente"}</td>
            `;

            row.addEventListener("click", () => {
                window.location.href = `/veiw/Client/src/details_Innovation.html?id=${inv.id}&from=user`;
            });

            table.appendChild(row);
        });

    } catch (err) {
        console.error("Erreur chargement innovations :", err);
        table.innerHTML = "<tr><td colspan='4'>Erreur de chargement</td></tr>";
    }
}


// =============================
// 4️⃣ DETAILS INNOVATION
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
// 5️⃣ RETOUR INTELLIGENT (admin/user/visitor)
// =============================
function initRetour() {
    const btn = document.getElementById("btn-retour");
    if (!btn) return;

    const params = new URLSearchParams(window.location.search);
    const from = params.get("from") ?? "visitor";

    btn.addEventListener("click", () => {

        if (from === "admin") {
            window.location.href = "/veiw/Admin/a_Innovation.html";
            return;
        }

        if (from === "user") {
            window.location.href = "/veiw/Client/src/list_Innovation.html";
            return;
        }

        // ⭐ VISITEUR : retour vers catégorie AVEC ID obligatoire
        if (window.currentCategoryId) {
            window.location.href =
                `/veiw/Client/src/category_details.html?id=${window.currentCategoryId}`;
        } else {
            // fallback si jamais
            window.location.href = "/veiw/Client/src/categories.html";
        }
    });
}


// =============================
// 6️⃣ BACKOFFICE – ADMIN
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
            const categoryName = catMap[inv.category_id] ?? "Inconnue";

            row.id = `row-${inv.id}`;
            row.innerHTML = `
                <td onclick="ouvrirDetails(${inv.id})" style="cursor:pointer;color:#8A8DFF;">${inv.titre}</td>
                <td>${categoryName}</td>
                <td>${inv.date_creation}</td>
                <td id="statut-${inv.id}">${inv.statut ?? "En attente"}</td>
                <td>
                    <button class="valider" onclick="validerInnovation(${inv.id})">Valider</button>
                    <button class="rejeter" onclick="rejeterInnovation(${inv.id})">Rejeter</button>
                    <button class="btn-edit" onclick="modifierInnovation(${inv.id})">Modifier</button>
                    <button class="delete" onclick="deleteInnovation(${inv.id})">Supprimer</button>
                </td>
            `;

            table.appendChild(row);
        });

    } catch (err) {
        console.error("Erreur admin :", err);
    }
}

function ouvrirDetails(id) {
    window.location.href = `/veiw/Client/src/details_Innovation.html?id=${id}&from=admin`;
}


// =============================
// 7️⃣ VALIDATION / REJET
// =============================
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


// =============================
// 8️⃣ DELETE
// =============================
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


// =============================
// 9️⃣ EDITION ADMIN
// =============================
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
