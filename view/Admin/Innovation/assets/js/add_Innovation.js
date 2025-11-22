console.log("add_Innovation.js chargé ✓");

// Sidebar toggle
const sidebarToggle = document.getElementById("sidebarToggle");
if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
        document.body.classList.toggle("sidebar-collapsed");
    });
}

// Thème toggle
const themeToggle = document.getElementById("themeToggle");
if (themeToggle) {
    themeToggle.addEventListener("click", () => {
        document.body.classList.toggle("light");
    });
}

// Dropdown animation
document.querySelectorAll(".menu-dropdown").forEach(drop => {
    const link = drop.querySelector(".menu-link");
    if (!link) return;

    link.addEventListener("click", e => {
        e.preventDefault();
        drop.classList.toggle("open");
    });
});
// =============================
// Validation du formulaire
// =============================

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const titre = document.getElementById("titre");
    const description = document.getElementById("description");
    const categorie = document.getElementById("categorie_id");

    form.addEventListener("submit", (e) => {
        let valid = true;
        let message = "";

        if (titre.value.trim() === "") {
            valid = false;
            message += "❌ Le titre est obligatoire.\n";
        }

        if (description.value.trim() === "") {
            valid = false;
            message += "❌ La description est obligatoire.\n";
        }

        if (categorie.value === "") {
            valid = false;
            message += "❌ Vous devez choisir une catégorie.\n";
        }

        if (!valid) {
            e.preventDefault();
            alert(message);
        }
    });
});
