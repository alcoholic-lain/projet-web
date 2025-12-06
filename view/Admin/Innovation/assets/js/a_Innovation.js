function confirmInnovation(id, statut) {

    if (!confirm("Confirmer le changement de statut : " + statut + " ?")) {
        return;
    }

    fetch('/projet-web/controller/api/admin/confirm_innovation.php', {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `innovation_id=${encodeURIComponent(id)}&statut=${encodeURIComponent(statut)}`
    })
        .then(response => response.text())
        .then(text => {
            console.log("Réponse brute API :", text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                alert("❌ Réponse invalide de l'API");
                return;
            }

            if (data.success) {
                alert("✅ Statut mis à jour + email envoyé !");
                location.reload();
            } else {
                alert("❌ Erreur API : " + (data.error || "inconnue"));
            }
        })
        .catch(err => {
            console.error("Erreur réseau :", err);
            alert("❌ Erreur réseau");
        });
}
console.log("a_Innovation.js chargé");

// ----- TRI TABLE -----
let currentSortColumn = -1;
let currentSortDirection = 1;

function sortInnovationTable(colIndex) {
    const table = document.getElementById("tableInno");
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));

    if (currentSortColumn === colIndex) {
        currentSortDirection *= -1;
    } else {
        currentSortColumn = colIndex;
        currentSortDirection = 1;
    }

    rows.sort((a, b) => {
        const aText = a.children[colIndex].innerText.trim().toLowerCase();
        const bText = b.children[colIndex].innerText.trim().toLowerCase();
        return aText.localeCompare(bText) * currentSortDirection;
    });

    tbody.innerHTML = "";
    rows.forEach(row => tbody.appendChild(row));

    updateSortVisual(colIndex);
}

function updateSortVisual(colIndex) {
    document.querySelectorAll("th").forEach(th => {
        th.classList.remove("sorted");
        const arrow = th.querySelector(".sort-arrow");
        if (arrow) arrow.remove();
    });

    const th = document.querySelectorAll("th")[colIndex];
    th.classList.add("sorted");

    const arrow = document.createElement("span");
    arrow.className = "sort-arrow";
    arrow.innerHTML = currentSortDirection === 1 ? "▲" : "▼";
    th.appendChild(arrow);
}

// ----- EXPORT EXCEL -----
function exportTableToExcel(tableId = 'tableInno') {
    const table = document.getElementById(tableId);
    const html = table.outerHTML.replace(/ /g, '%20');

    const a = document.createElement('a');
    a.href = 'data:application/vnd.ms-excel,' + html;
    a.download = 'innovations.xls';
    a.click();
}

// ----- EXPORT PDF -----
function exportTableToPDF(tableId = 'tableInno') {
    const table = document.getElementById(tableId);

    const html = `
        <html>
        <head><title>Export PDF</title></head>
        <body>${table.outerHTML}</body>
        </html>`;

    const win = window.open('', '', 'height=700,width=900');
    win.document.write(html);
    win.print();
}

