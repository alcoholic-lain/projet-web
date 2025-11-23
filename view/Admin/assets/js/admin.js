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
const themeSwitch = document.querySelector(".theme-switcher");

if (themeSwitch) {
    themeSwitch.addEventListener("click", () => {
        body.classList.toggle("light");

        // Sauvegarde du thème
        localStorage.setItem(
            "theme",
            body.classList.contains("light") ? "light" : "dark"
        );
    });
}

// Charger thème sauvegardé
const savedTheme = localStorage.getItem("theme");
if (savedTheme === "light") {
    body.classList.add("light");
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
