// =============================
// CATEGORY MODULE JS
// Auteur : Hichem Challakhi 🚀
// =============================

const API_URL = "/controller/components/CategoryController.php";

/* ============================================================
   🔧 UTILITAIRE
============================================================ */
function showError(message) {
    const loading = document.getElementById("loading");
    if (loading) {
        loading.innerHTML = `
            <i class="fas fa-exclamation-triangle text-4xl text-red-600"></i>
            <p class="mt-4 text-red-600">${message}</p>
        `;
    } else {
        alert(message);
    }
}

/* ============================================================
   1️⃣ LOAD CATEGORIES — FRONT (categories.html)
============================================================ */
async function loadCategoriesFront() {
    const loading = document.getElementById("loading");
    const grid = document.getElementById("categories-grid");
    const list = document.getElementById("categories-list");

    // Si on n'est pas sur la page front, on sort
    if (!loading || !grid || !list) return;

    loading.classList.remove("hidden");
    grid.classList.add("hidden");
    list.classList.add("hidden");

    try {
        const response = await fetch(API_URL);
        const data = await response.json();

        if (!data.success || !data.records) {
            showError("Failed to load categories");
            return;
        }

        const categories = data.records;
        loading.classList.add("hidden");

        if (categories.length === 0) {
            document.getElementById("empty-state")?.classList.remove("hidden");
            return;
        }

        // === GRID DISPLAY ===
        grid.innerHTML = categories.map(cat => `
            <div class="category-card bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700" 
                 data-id="${cat.id}">
                <h3 class="text-xl font-bold mb-2">${cat.nom}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">${cat.description}</p>
            </div>
        `).join("");

        // === LIST DISPLAY ===
        list.innerHTML = categories.map(cat => `
            <div class="category-card bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border flex items-center space-x-6" 
                 data-id="${cat.id}">
                <div class="flex-1">
                    <h3 class="text-xl font-bold mb-1">${cat.nom}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">${cat.description}</p>
                </div>
            </div>
        `).join("");

        // === Cartes cliquables → page de détails
        document.querySelectorAll(".category-card").forEach(card => {
            card.addEventListener("click", () => {
                const id = card.dataset.id;
                window.location.href = `/veiw/Client/src/category_details.html?id=${id}`;
            });
        });

        grid.classList.remove("hidden");

    } catch (error) {
        console.error("Error loading categories:", error);
        showError("Error loading categories");
    }
}

/* ============================================================
   2️⃣ LOAD CATEGORIES — BACKOFFICE (a_Category.html)
============================================================ */
async function loadCategoriesAdmin() {
    const table = document.getElementById("category-table");
    if (!table) return; // Pas sur la page admin

    try {
        const response = await fetch(API_URL);
        const data = await response.json();

        table.innerHTML = "";

        if (data.success && data.records && data.records.length > 0) {
            data.records.forEach(cat => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${cat.id}</td>
                    <td>${cat.nom}</td>
                    <td>${cat.description}</td>
                    <td>${new Date(cat.date_creation).toLocaleDateString("fr-FR")}</td>
                    <td>
                        <button class="btn-edit" onclick="editCategory(${cat.id})">✏️ Modifier</button>
                        <button class="btn-delete" onclick="deleteCategory(${cat.id})">🗑️ Supprimer</button>
                    </td>
                `;
                table.appendChild(row);
            });
        } else {
            table.innerHTML =
                "<tr><td colspan='5' style='text-align:center;'>Aucune catégorie trouvée</td></tr>";
        }
    } catch (err) {
        console.error("Erreur chargement catégories admin :", err);
        table.innerHTML =
            "<tr><td colspan='5' style='text-align:center;color:#FF6B6B;'>Erreur de chargement</td></tr>";
    }
}

/* ============================================================
   3️⃣ AJOUTER UNE CATÉGORIE (add_Category.html)
============================================================ */
function setupAddCategory() {
    const form = document.getElementById("form-category");
    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const nom = document.getElementById("nom").value.trim();
        const description = document.getElementById("description").value.trim();
        const msg = document.getElementById("msg");

        if (!nom || !description) {
            msg.textContent = "⚠️ Tous les champs sont obligatoires.";
            msg.className = "error";
            return;
        }

        try {
            const response = await fetch(API_URL, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ nom, description })
            });

            const data = await response.json();

            if (data.success) {
                msg.textContent = "✅ Catégorie créée avec succès !";
                msg.className = "success";

                setTimeout(() => {
                    window.location.href = "a_Category.html";
                }, 1200);
            } else {
                msg.textContent = "❌ " + (data.message || "Erreur lors de la création");
                msg.className = "error";
            }
        } catch (err) {
            console.error("Erreur création catégorie :", err);
            msg.textContent = "❌ Erreur serveur";
            msg.className = "error";
        }
    });
}

/* ============================================================
   4️⃣ NAVIGUER VERS LA PAGE EDIT
============================================================ */
function editCategory(id) {
    window.location.href = `edit_Category.html?id=${id}`;
}

/* ============================================================
   5️⃣ ÉDITER UNE CATÉGORIE (edit_Category.html)
============================================================ */
async function setupEditCategory() {
    const form = document.getElementById("form-edit-category");
    if (!form) return;

    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");

    const msg = document.getElementById("msg");
    const idInput = document.getElementById("category-id");
    const nomInput = document.getElementById("nom");
    const descInput = document.getElementById("description");

    if (!id) {
        msg.textContent = "❌ ID de catégorie manquant.";
        msg.className = "error";
        return;
    }

    idInput.value = id;

    // --- Charger la catégorie ---
    try {
        const res = await fetch(`${API_URL}?id=${id}`);
        const cat = await res.json();

        if (!cat || cat.success === false) {
            msg.textContent = "❌ Catégorie introuvable.";
            msg.className = "error";
            return;
        }

        nomInput.value = cat.nom;
        descInput.value = cat.description;
    } catch (err) {
        console.error("Erreur chargement catégorie :", err);
        msg.textContent = "❌ Erreur serveur lors du chargement.";
        msg.className = "error";
    }

    // --- Submit (PUT) ---
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const nom = nomInput.value.trim();
        const description = descInput.value.trim();

        if (!nom || !description) {
            msg.textContent = "⚠️ Tous les champs sont obligatoires.";
            msg.className = "error";
            return;
        }

        try {
            const res = await fetch(API_URL, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id, nom, description })
            });

            const data = await res.json();

            if (data.success) {
                msg.textContent = "✅ Catégorie mise à jour avec succès !";
                msg.className = "success";

                setTimeout(() => {
                    window.location.href = "a_Category.html";
                }, 1200);
            } else {
                msg.textContent = "❌ " + (data.message || "Erreur lors de la mise à jour");
                msg.className = "error";
            }
        } catch (err) {
            console.error("Erreur update catégorie :", err);
            msg.textContent = "❌ Erreur serveur";
            msg.className = "error";
        }
    });
}

/* ============================================================
   6️⃣ SUPPRIMER UNE CATÉGORIE
============================================================ */
async function deleteCategory(id) {
    if (!confirm("Êtes-vous sûr de vouloir supprimer cette catégorie ?")) return;

    try {
        const res = await fetch(API_URL, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        });

        const data = await res.json();

        if (data.success) {
            alert("✅ Catégorie supprimée avec succès !");
            loadCategoriesAdmin(); // rechargement du tableau
        } else {
            alert("❌ " + (data.message || "Erreur lors de la suppression"));
        }
    } catch (err) {
        console.error("Erreur suppression catégorie :", err);
        alert("❌ Erreur serveur");
    }
}

/* ============================================================
   7️⃣ ROUTING AUTO SELON LA PAGE
============================================================ */
document.addEventListener("DOMContentLoaded", () => {
    const path = location.pathname;

    // Front
    if (path.includes("categories.html"))
        loadCategoriesFront();

    // Admin
    if (path.includes("a_Category.html"))
        loadCategoriesAdmin();

    if (path.includes("add_Category.html"))
        setupAddCategory();

    if (path.includes("edit_Category.html"))
        setupEditCategory();
});

// On expose les fonctions utilisées en inline (onclick)
window.editCategory = editCategory;
window.deleteCategory = deleteCategory;
