document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("addCategoryForm");
    const nom = document.getElementById("nom");
    const description = document.getElementById("description");

    form.addEventListener("submit", (e) => {

        let errors = [];

        if (nom.value.trim() === "") errors.push("❌ Le nom est obligatoire.");
        if (description.value.trim() === "") errors.push("❌ La description est obligatoire.");

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join("\n"));
        }
    });

});
