document.addEventListener("DOMContentLoaded", function(){
    document.getElementById("resetPasswordForm").addEventListener("submit", function(e){
        e.preventDefault();

        const token = document.getElementById("token").value.trim();
        const newPassword = document.getElementById("newPassword").value.trim();
        const confirmPassword = document.getElementById("confirmPassword").value.trim();
        const message = document.getElementById("message");

        message.textContent = "";
        message.style.color = "red";

        if (!token) {
            message.textContent = "Token manquant. Veuillez utiliser le lien correct.";
            return;
        }

        if (newPassword.length < 6){
            message.textContent = "Veuillez saisir un mot de passe de taille ≥ 6 caractères.";
            return;
        }

        if (newPassword === "" || confirmPassword === "" ) {
            message.textContent = "Veuillez remplir tous les champs.";
            return;
        }

        if (newPassword !== confirmPassword) {
            message.textContent = "Les mots de passe ne correspondent pas.";
            return;
        }

        fetch("../../controller/UserC/resetPassword.php", {
            method: "POST",
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `token=${encodeURIComponent(token)}&newPassword=${encodeURIComponent(newPassword)}`
        })
            .then(res => res.json())
            .then(data => {
                // Affiche le message renvoyé par le serveur
                if (data.success) {
                    message.style.color = "green";
                    message.textContent = data.message || "Mot de passe réinitialisé avec succès.";
                    setTimeout(() => {
                        window.location.href = "login.html";
                    }, 2000);
                } else {
                    message.style.color = "red"; // pour les erreurs
                    message.textContent = data.message || "Une erreur est survenue.";
                }
            })
            .catch(err => {
                message.style.color = "red";
                message.textContent = "Erreur serveur. Veuillez réessayer.";
                console.error(err);
            });
    });
});
