// === Sidebar toggle ===
const body = document.body;
const sidebarToggle = document.getElementById("sidebarToggle");
const themeToggle = document.getElementById("themeToggle");
const dropdowns = document.querySelectorAll(".menu-dropdown");

if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
        body.classList.toggle("sidebar-collapsed");
    });
}

// === Theme toggle (jour / nuit) ===
if (themeToggle) {
    themeToggle.addEventListener("click", () => {
        body.classList.toggle("light");
        // Optionnel : on peut stocker la préférence dans localStorage
        // localStorage.setItem("theme", body.classList.contains("light") ? "light" : "dark");
    });
}

// === Dropdown menus (Catégories / Innovations) ===
dropdowns.forEach(drop => {
    const link = drop.querySelector(".menu-link");
    if (!link) return;
    link.addEventListener("click", (e) => {
        e.preventDefault();
        drop.classList.toggle("open");
    });
});
