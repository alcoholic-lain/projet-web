document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("loginForm");
    const messageBox = document.getElementById("messageBox");

    if (!form || !messageBox) return;

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();

        if (!email || !password) {
            showMessage("Veuillez remplir tous les champs !", "error");
            return;
        }

        fetch("../../controller/LoginController.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, "success");

                    // Redirection selon role_id (renvoyé par le PHP)
                    setTimeout(() => {
                        if (data.role_id == 1) {
                            // Admin
                            window.location.href = "../BackEnd/dashboard.php";
                        } else if (data.role_id == 2) {
                            // Utilisateur
                            window.location.href = "profile.php";
                        }
                    }, 1000);
                } else {
                    showMessage(data.message || "Email ou mot de passe incorrect.", "error");
                }
            })
            .catch(err => {
                console.error("Erreur fetch:", err);
                showMessage("Erreur serveur, veuillez réessayer.", "error");
            });
    });

    function showMessage(msg, type) {
        messageBox.textContent = msg;
        messageBox.className = `message-box ${type}`;
        messageBox.style.display = "block";

        // Disparaît automatiquement après 5s si succès
        if (type === "success") {
            setTimeout(() => { messageBox.style.display = "none"; }, 5000);
        }
    }
});
