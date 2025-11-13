// =============================
// INNOVATION MODULE JS (BACKEND CONNECTED)
// Auteur : Hichem Challakhi 🚀
// =============================

const API = "/controller/components/InnovationController.php";
const API_CAT = "/controller/components/CategoryController.php";

/* ============================================================
   1️⃣ CHARGER LISTE DES CATÉGORIES (Pour add + edit)
============================================================ */
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

/* ============================================================
   2️⃣ AJOUTER UNE INNOVATION
============================================================ */
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
            const response = await fetch(API, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

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

/* ============================================================
   3️⃣ AFFICHER LISTE DES INNOVATIONS (Front)
============================================================ */
async function afficherListe() {
    const table = document.getElementById("innovation-list");
    if (!table) return;

    try {
        const res = await fetch(API);
        const data = await res.json();

        table.innerHTML = `
            <tr>
                <th>Titre</th>
                <th>Catégorie ID</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>
        `;

        data.records.forEach(inv => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td style="cursor:pointer;color:#6C63FF">
                    ${inv.titre}
                </td>
                <td>${inv.category_id}</td>
                <td>${inv.date_creation}</td>
                <td>${inv.statut ?? "En attente"}</td>
            `;

            row.addEventListener("click", () => {
                window.location.href = `/veiw/Client/src/details_Innovation.html?id=${inv.id}`;
            });

            table.appendChild(row);
        });

    } catch (err) {
        console.error("Erreur chargement innovations :", err);
        table.innerHTML = "<tr><td colspan='4'>Erreur de chargement</td></tr>";
    }
}

/* ============================================================
   4️⃣ AFFICHER DETAILS D'UNE INNOVATION
============================================================ */
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

    } catch (err) {
        console.error("Erreur chargement détails innovation :", err);
    }
}

/* ============================================================
   5️⃣ BACKOFFICE – LISTE + ACTIONS (Admin)
============================================================ */
async function afficherAdmin() {
    const table = document.getElementById("admin-table");
    if (!table) return;

    try {
        const res = await fetch(API);
        const data = await res.json();

        table.innerHTML = "";

        data.records.forEach(inv => {
            const row = document.createElement("tr");
            row.id = `row-${inv.id}`;

            row.innerHTML = `
                <td onclick="ouvrirDetails(${inv.id})"
                    style="cursor:pointer;color:#8A8DFF;">
                    ${inv.titre}
                </td>

                <td>${inv.category_id}</td>
                <td>${inv.date_creation}</td>

                <td id="statut-${inv.id}">
                    ${inv.statut ?? "En attente"}
                </td>

                <td>
                    <button class="valider" onclick="validerInnovation(${inv.id})">Valider</button>
                    <button class="rejeter" onclick="rejeterInnovation(${inv.id})">Rejeter</button>
                    <button class="delete" onclick="deleteInnovation(${inv.id})">Supprimer</button>
                </td>
            `;

            table.appendChild(row);
        });

    } catch (err) {
        console.error("Erreur chargement admin :", err);
        table.innerHTML = "<tr><td colspan='5'>Erreur chargement</td></tr>";
    }
}

function ouvrirDetails(id) {
    window.location.href = `/veiw/Client/src/details_Innovation.html?id=${inv.id}`;
}

/* ------------------------------------------------------------
   VALIDATION / REJET
------------------------------------------------------------ */
async function validerInnovation(id) {
    updateStatut(id, "Validée");
}
async function rejeterInnovation(id) {
    updateStatut(id, "Rejetée");
}

async function updateStatut(id, statut) {
    try {
        const res = await fetch(API, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id: id,
                statut: statut
            })
        });

        const data = await res.json();

        if (data.success) {
            document.getElementById(`statut-${id}`).textContent = statut;
        }

    } catch (err) {
        console.error("Erreur changement statut :", err);
    }
}

/* ------------------------------------------------------------
   SUPPRIMER INNOVATION
------------------------------------------------------------ */
async function deleteInnovation(id) {
    if (!confirm("Voulez-vous supprimer cette innovation ?")) return;

    try {
        const res = await fetch(API, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id })
        });

        const data = await res.json();

        if (data.success) {
            document.getElementById(`row-${id}`).remove();
        }

    } catch (err) {
        console.error("Erreur suppression :", err);
    }
}

/* ============================================================
   6️⃣ EDITION (BackOffice)
============================================================ */
async function setupEditInnovationPage() {
    const form = document.getElementById("form-edit-innovation");
    if (!form) return;

    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");

    if (!id) return;

    const msg = document.getElementById("msg");
    const titreEl = document.getElementById("titre");
    const catSelect = document.getElementById("category_id");
    const descEl = document.getElementById("description");
    const statutEl = document.getElementById("statut");
    const hiddenId = document.getElementById("innovation-id");

    hiddenId.value = id;

    try {
        const resCat = await fetch(API_CAT);
        const dataCat = await resCat.json();

        dataCat.records.forEach(cat => {
            const opt = document.createElement("option");
            opt.value = cat.id;
            opt.textContent = cat.nom;
            catSelect.appendChild(opt);
        });
    } catch (err) {
        console.error("Erreur chargement catégories :", err);
    }

    try {
        const res = await fetch(`${API}?id=${id}`);
        const inv = await res.json();

        if (!inv || inv.success === false) {
            msg.textContent = "❌ Innovation introuvable";
            msg.style.color = "red";
            return;
        }

        titreEl.value = inv.titre;
        descEl.value = inv.description;
        statutEl.value = inv.statut ?? "En attente";
        catSelect.value = inv.category_id;

    } catch (err) {
        console.error("Erreur chargement innovation :", err);
        msg.textContent = "❌ Erreur serveur lors du chargement";
        msg.style.color = "red";
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const titre = titreEl.value.trim();
        const description = descEl.value.trim();
        const category_id = catSelect.value;
        const statut = statutEl.value;

        if (titre.length < 3) {
            msg.textContent = "⚠️ Le titre doit contenir au moins 3 caractères.";
            msg.style.color = "orange";
            return;
        }
        if (description.length < 10) {
            msg.textContent = "⚠️ La description doit contenir au moins 10 caractères.";
            msg.style.color = "orange";
            return;
        }
        if (!category_id) {
            msg.textContent = "⚠️ Vous devez choisir une catégorie.";
            msg.style.color = "orange";
            return;
        }

        const payload = {
            id: id,
            titre,
            description,
            category_id,
            statut
        };

        try {
            const res = await fetch(API, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (data.success) {
                msg.textContent = "✅ Innovation mise à jour avec succès !";
                msg.style.color = "lightgreen";

                setTimeout(() => {
                    window.location.href = "a_Innovation.html";
                }, 1500);

            } else {
                msg.textContent = "❌ " + (data.message || "Erreur de mise à jour");
                msg.style.color = "red";
            }

        } catch (err) {
            console.error("Erreur update :", err);
            msg.textContent = "❌ Erreur serveur";
            msg.style.color = "red";
        }
    });
}

/* ============================================================
   7️⃣ INITIALISATION AUTO
============================================================ */
document.addEventListener("DOMContentLoaded", () => {
    const path = location.pathname;

    if (path.includes("add_Innovation.html")) {
        loadCategories();
        setupAddInnovationPage();
    }

    if (path.includes("list_Innovation.html"))
        afficherListe();

    if (path.includes("details_Innovation.html"))
        afficherDetails();

    if (path.includes("a_Innovation.html"))
        afficherAdmin();

    if (path.includes("edit_Innovation.html"))
        setupEditInnovationPage();
});
