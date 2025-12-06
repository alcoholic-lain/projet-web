// JS spécifique à la page a_Category.php
console.log("a_Category.js chargé 🔥");

/* ====================================================
   📄 EXPORT EXCEL
   ==================================================== */
function exportTableToExcel() {
    const table = document.querySelector("table");
    const html = table.outerHTML;

    const blob = new Blob([html], { type: "application/vnd.ms-excel" });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = "innovations.xls";
    a.click();
}

/* ====================================================
   📘 EXPORT PDF
   ==================================================== */
function exportTableToPDF() {
    const table = document.querySelector("table");

    const w = window.open("");
    w.document.write("<html><head><title>PDF</title></head><body>");
    w.document.write(table.outerHTML);
    w.document.write("</body></html>");
    w.document.close();
    w.print();
}
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