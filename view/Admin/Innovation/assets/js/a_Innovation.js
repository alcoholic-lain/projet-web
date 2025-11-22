// === a_Innovation.js – JS spécifique à la page & layout ===

console.log("a_Innovation.js chargé ✓");

const body = document.body;
const sidebarToggle = document.getElementById("sidebarToggle");
const themeToggle = document.getElementById("themeToggle");
const dropdowns = document.querySelectorAll(".menu-dropdown");

// Toggle sidebar
if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
        body.classList.toggle("sidebar-collapsed");
    });
}

// Toggle thème (jour/nuit)
if (themeToggle) {
    themeToggle.addEventListener("click", () => {
        body.classList.toggle("light");
    });
}

// Dropdown (Catégories / Innovations)
dropdowns.forEach(drop => {
    const link = drop.querySelector(".menu-link");
    if (!link) return;
    link.addEventListener("click", (e) => {
        e.preventDefault();
        drop.classList.toggle("open");
    });
});
