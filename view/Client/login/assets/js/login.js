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

        // ⭐⭐ URL RELATIVE = fonctionne partout
        const loginURL = "/projet-web/controller/components/login/LoginController.php";

        fetch(loginURL, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, "success");

                    setTimeout(() => {
                        if (data.role_id == 1) {
                            window.location.href = "/projet-web/view/Admin/index.php";
                        } else {
                            window.location.href = "/projet-web/view/Client/index.php";
                        }
                    }, 800);
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
    }
});
