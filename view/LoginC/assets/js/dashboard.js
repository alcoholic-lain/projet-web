document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const table = document.querySelector(".table-container table tbody");
    const rows = Array.from(table.getElementsByTagName("tr"));

    searchInput.addEventListener("input", function() {
        const query = this.value.toLowerCase();

        rows.forEach(row => {
            const pseudo = row.cells[1].textContent.toLowerCase();
            const email = row.cells[2].textContent.toLowerCase();
            const role = row.cells[4].textContent.toLowerCase();

            if (pseudo.includes(query) || email.includes(query) || role.includes(query)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
    // Fonction utilitaire pour créer un chart
    function createLineChart(ctx, label, labels, values, colorBorder, colorBg) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: values,
                    borderColor: colorBorder,
                    backgroundColor: colorBg,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // --- Connexions Chart ---
    fetch('../../controller/UserC/DashboardController.php?type=connexions')
        .then(res => res.json())
        .then(data => {
            const ctxConn = document.getElementById('connexionsChart').getContext('2d');
            createLineChart(ctxConn, 'Connexions', data.labels, data.values, 'rgba(75, 192, 192, 1)', 'rgba(75, 192, 192, 0.2)');
        })
        .catch(err => console.error("Erreur fetch connexions:", err));

    // --- Modifications Chart ---
    fetch('../../controller/UserC/DashboardController.php?type=modifications')
        .then(res => res.json())
        .then(data => {
            const ctxMod = document.getElementById('modificationsChart').getContext('2d');
            createLineChart(ctxMod, 'Modifications', data.labels, data.values, 'rgba(255, 159, 64, 1)', 'rgba(255, 159, 64, 0.2)');
        })
        .catch(err => console.error("Erreur fetch modifications:", err));



});


// Tri alterné ▲ / ▼ sur la colonne Pseudo
document.getElementById("colPseudo").onclick = function() {

    const th = document.getElementById("colPseudo");
    const table = document.getElementById("userTable");
    const tbody = table.getElementsByTagName("tbody")[0];
    const rows = Array.from(tbody.rows);

    // Déterminer si on doit trier en ascendant ou descendant
    let ascending = true;

    if (th.classList.contains("sorted-asc")) {
        ascending = false; // si déjà trié ascendant → on passe à descendant
    }

    // Réinitialiser les flèches sur tous les th
    document.querySelectorAll("th").forEach(x => {
        x.classList.remove("sorted-asc", "sorted-desc");
    });

    // Appliquer la nouvelle flèche
    if (ascending) th.classList.add("sorted-asc");
    else th.classList.add("sorted-desc");

    // Tri réel
    rows.sort((a, b) => {
        let pseudoA = a.cells[1].innerText.trim().toLowerCase();
        let pseudoB = b.cells[1].innerText.trim().toLowerCase();

        return ascending
            ? pseudoA.localeCompare(pseudoB)   // ▲ ascendant
            : pseudoB.localeCompare(pseudoA);  // ▼ descendant
    });

    // Réinjecter les lignes triées
    rows.forEach(row => tbody.appendChild(row));
};

// dashboard.js


// Charts
new Chart(document.getElementById('connexionsChart'), {
    type: 'line',
    data: {
        labels: connLabels,
        datasets: [{
            label: 'Connexions',
            data: connValues,
            borderColor: '#b38cff',
            backgroundColor: 'rgba(179, 140, 255, 0.2)',
            fill: true,
            tension: 0.4
        }]
    },
    options: { plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('modificationsChart'), {
    type: 'bar',
    data: {
        labels: modLabels,
        datasets: [{
            label: 'Modifications',
            data: modValues,
            backgroundColor: '#5ee7c0'
        }]
    },
    options: { plugins: { legend: { display: false } } }
});









