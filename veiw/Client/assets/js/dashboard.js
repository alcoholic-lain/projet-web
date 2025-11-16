const API_CAT = "http://localhost/projet-web/controller/components/CategoryController.php";
const API_INNOV = "http://localhost/projet-web/controller/components/InnovationController.php";

async function loadAdminStats() {
    try {
        const catRes = await fetch(API_CAT);
        const innovRes = await fetch(API_INNOV);

        const categories = (await catRes.json()).records || [];
        const innovations = (await innovRes.json()).records || [];

        // ==== STATS ====
        document.getElementById("stats-cat").textContent = categories.length;
        document.getElementById("stats-innov").textContent = innovations.length;

        const pending = innovations.filter(i => i.statut === "En attente").length;
        document.getElementById("stats-pending").textContent = pending;


        // ==== MATCH CATEGORY NAME + COUNT ====
        const countByCategory = {};

        categories.forEach(cat => {
            countByCategory[cat.nom] = 0;
        });

        innovations.forEach(inv => {
            const cat = categories.find(c => c.id == inv.category_id);
            if (cat) countByCategory[cat.nom]++;
        });

        const labels = Object.keys(countByCategory);
        const values = Object.values(countByCategory);

        // ==== BUILD GRAPH ====
        if (labels.length > 0) {
            new Chart(document.getElementById("chartCats"), {
                type: "bar",
                data: {
                    labels,
                    datasets: [{
                        label: "Innovations",
                        data: values,
                        backgroundColor: "#8A8DFF",
                        borderRadius: 8
                    }]
                },
                options: {
                    plugins: { legend: { display: false }},
                    scales: {
                        x: { ticks: { color: "#C5C7F7" }},
                        y: { ticks: { color: "#C5C7F7" }}
                    }
                }
            });
        }

    } catch (err) {
        console.error("Erreur stats dashboard:", err);
    }
}

document.addEventListener("DOMContentLoaded", loadAdminStats);
