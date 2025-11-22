// =============================
// Validation JS pour edit_Innovation
// =============================

document.addEventListener("DOMContentLoaded", () => {

    const form = document.querySelector("form");
    const titre = document.getElementById("titre");
    const description = document.getElementById("description");
    const categorie = document.querySelector("select[name='categorie_id']");
    const statut = document.querySelector("select[name='statut']");

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

        if (!categorie.value || categorie.value === "") {
            valid = false;
            message += "❌ Vous devez choisir une catégorie.\n";
        }

        if (!statut.value || statut.value === "") {
            valid = false;
            message += "❌ Le statut est obligatoire.\n";
        }

        if (!valid) {
            e.preventDefault();
            alert(message);
        }
    });

});
