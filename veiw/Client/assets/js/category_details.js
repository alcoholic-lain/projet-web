const API_CAT = "/controller/components/CategoryController.php";
const API_INNOV = "/controller/components/InnovationController.php";

async function loadCategoryPage() {
    const params = new URLSearchParams(window.location.search);
    const categoryId = params.get("id");

    if (!categoryId) return;

    /* ------------------------------
       1️⃣ Load Category Info
    ------------------------------ */
    try {
        const catRes = await fetch(`${API_CAT}?id=${categoryId}`);
        const category = await catRes.json();

        document.getElementById("category-name").textContent = category.nom;
        document.getElementById("category-description").textContent = category.description;
    } catch (err) {
        console.error("Erreur chargement catégorie :", err);
    }

    /* ------------------------------
       2️⃣ Load innovations by category
    ------------------------------ */
    try {
        const innovRes = await fetch(`${API_INNOV}?category_id=${categoryId}`);
        const data = await innovRes.json();

        const loading = document.getElementById("loading");
        const grid = document.getElementById("innovation-grid");
        const empty = document.getElementById("empty");

        // Hide loading spinner
        loading.classList.add("hidden");

        if (!data.success) {
            empty.classList.remove("hidden");
            return;
        }

        // ⭐ FILTRER VISITEUR :
        // Ne garder QUE les innovations Validées
        const validated = data.records.filter(inv => inv.statut === "Validée");

        if (validated.length === 0) {
            empty.classList.remove("hidden");
            return;
        }

        // Fill grid
        grid.innerHTML = validated.map(innov => `
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border cursor-pointer transition hover:shadow-xl"
                onclick="window.location.href='details_Innovation.html?id=${innov.id}&from=visitor'">

                <h3 class="text-xl font-bold mb-2">${innov.titre}</h3>

                <p class="text-gray-600 dark:text-gray-400">
                    ${innov.description.substring(0, 120)}...
                </p>

                <p class="text-sm text-indigo-600 mt-4">Read more →</p>
            </div>
        `).join("");

        grid.classList.remove("hidden");

    } catch (err) {
        console.error("Erreur chargement innovations :", err);
    }
}

document.addEventListener("DOMContentLoaded", loadCategoryPage);
