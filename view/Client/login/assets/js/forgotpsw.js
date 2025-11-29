document.getElementById("forgotPasswordForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const msg = document.getElementById("message");
    msg.textContent = "";
    msg.style.color = "red";

    const form = document.getElementById("forgotPasswordForm");

    // Récupération de tous les champs
    const formData = new FormData(form);
    const email = formData.get("email").trim();
    const pseudo = formData.get("pseudo").trim();

    if (!email || !pseudo) {
        msg.textContent = "Veuillez saisir votre email et votre pseudo.";
        return;
    }

    const params = new URLSearchParams(formData);

    // Debug : vérifier ce qui est envoyé
    console.log("Données envoyées:", params.toString());

    fetch("http://localhost/projet-web/controller/components/login/ForgotPasswordController.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: params.toString()
    })
        .then(response => {
            console.log("Status HTTP:", response.status);
            return response.json();
        })
        .then(data => {
            console.log("Réponse serveur:", data);
            if (data.success) {
                window.location.href = `resetpassword.php?token=${encodeURIComponent(data.token)}`;
            } else {
                msg.textContent = data.message;
            }
        })
        .catch(error => {
            msg.textContent = "Erreur lors de l'envoi. Vérifiez votre serveur.";
            console.error("Erreur fetch:", error);
        });
});
