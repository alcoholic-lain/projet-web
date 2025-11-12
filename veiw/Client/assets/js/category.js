// =============================
// CATEGORY MODULE JS
// Auteur : Hichem Challakhi 🚀
// =============================

const API_URL = '../../controller/components/CategoryController.php';

// === LIST CATEGORIES (BackOffice) ===
async function loadCategories() {
    const table = document.getElementById("category-table");
    if (!table) return;

    try {
        const response = await fetch(API_URL);
        const data = await response.json();

        table.innerHTML = "";

        if (data.records && data.records.length > 0) {
            data.records.forEach((category) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${category.id}</td>
                    <td>${category.nom}</td>
                    <td>${category.description}</td>
                    <td>${new Date(category.date_creation).toLocaleDateString('fr-FR')}</td>
                    <td>
                        <button class="btn-edit" onclick="editCategory(${category.id})">✏️ Modifier</button>
                        <button class="btn-delete" onclick="deleteCategory(${category.id})">🗑️ Supprimer</button>
                    </td>
                `;
                table.appendChild(row);
            });
        } else {
            table.innerHTML = '<tr><td colspan="5" style="text-align:center;">Aucune catégorie trouvée</td></tr>';
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        table.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#FF6B6B;">Erreur lors du chargement des catégories</td></tr>';
    }
}

// === ADD CATEGORY ===
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
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    nom: nom,
                    description: description
                })
            });

            const data = await response.json();

            if (response.ok) {
                msg.textContent = "✅ Catégorie créée avec succès !";
                msg.className = "success";
                form.reset();

                setTimeout(() => {
                    window.location.href = "a_Category.html";
                }, 1500);
            } else {
                msg.textContent = "❌ " + (data.message || "Erreur lors de la création");
                msg.className = "error";
            }
        } catch (error) {
            console.error('Error creating category:', error);
            msg.textContent = "❌ Erreur lors de la création de la catégorie";
            msg.className = "error";
        }
    });
}

// === EDIT CATEGORY ===
function editCategory(id) {
    window.location.href = `edit_Category.html?id=${id}`;
}

async function setupEditCategory() {
    const form = document.getElementById("form-edit-category");
    if (!form) return;

    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");

    if (!id) {
        document.getElementById("msg").textContent = "❌ ID de catégorie manquant";
        document.getElementById("msg").className = "error";
        return;
    }

    // Load category data
    try {
        const response = await fetch(`${API_URL}?id=${id}`);
        const category = await response.json();

        if (response.ok) {
            document.getElementById("category-id").value = category.id;
            document.getElementById("nom").value = category.nom;
            document.getElementById("description").value = category.description;
        } else {
            document.getElementById("msg").textContent = "❌ Catégorie non trouvée";
            document.getElementById("msg").className = "error";
        }
    } catch (error) {
        console.error('Error loading category:', error);
        document.getElementById("msg").textContent = "❌ Erreur lors du chargement";
        document.getElementById("msg").className = "error";
    }

    // Handle form submission
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const categoryId = document.getElementById("category-id").value;
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
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: categoryId,
                    nom: nom,
                    description: description
                })
            });

            const data = await response.json();

            if (response.ok) {
                msg.textContent = "✅ Catégorie mise à jour avec succès !";
                msg.className = "success";

                setTimeout(() => {
                    window.location.href = "a_Category.html";
                }, 1500);
            } else {
                msg.textContent = "❌ " + (data.message || "Erreur lors de la mise à jour");
                msg.className = "error";
            }
        } catch (error) {
            console.error('Error updating category:', error);
            msg.textContent = "❌ Erreur lors de la mise à jour de la catégorie";
            msg.className = "error";
        }
    });
}

// === DELETE CATEGORY ===
async function deleteCategory(id) {
    if (!confirm("Êtes-vous sûr de vouloir supprimer cette catégorie ?")) {
        return;
    }

    try {
        const response = await fetch(API_URL, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id
            })
        });

        const data = await response.json();

        if (response.ok) {
            alert("✅ Catégorie supprimée avec succès !");
            loadCategories(); // Reload the list
        } else {
            alert("❌ " + (data.message || "Erreur lors de la suppression"));
        }
    } catch (error) {
        console.error('Error deleting category:', error);
        alert("❌ Erreur lors de la suppression de la catégorie");
    }
}

// === INITIALIZATION ===
document.addEventListener("DOMContentLoaded", () => {
    const path = location.pathname;

    if (path.includes("a_Category.html")) loadCategories();
    if (path.includes("add_Category.html")) setupAddCategory();
    if (path.includes("edit_Category.html")) setupEditCategory();
});
