// Validation formulaire catégorie

document.addEventListener("DOMContentLoaded", () => {

    const form = document.querySelector("form");
    const nom = document.getElementById("nom");
    const description = document.getElementById("description");

    form.addEventListener("submit", (e) => {

        let valid = true;
        let message = "";

        if (nom.value.trim() === "") {
            valid = false;
            message += "❌ Le nom est obligatoire.\n";
        }

        if (description.value.trim() === "") {
            valid = false;
            message += "❌ La description est obligatoire.\n";
        }

        if (!valid) {
            e.preventDefault();
            alert(message);
        }
    });

});
