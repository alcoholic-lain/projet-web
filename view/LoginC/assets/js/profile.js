document.addEventListener("DOMContentLoaded", () => {

    // === Fonctions d'affichage d'erreur/succès ===
    function createLabel(input, message, isSuccess = false) {
        if (!input || !input.parentElement) return;
        let container = input.parentElement.querySelector(".input-error");
        if (!container) {
            container = document.createElement("div");
            container.className = "input-error";
            input.parentElement.appendChild(container);
        }
        container.innerText = message;
        container.style.color = isSuccess ? "green" : "red";
        input.classList.remove("error-input", "success-input");
        input.classList.add(isSuccess ? "success-input" : "error-input");
        if (isSuccess) setTimeout(() => clearInputError(input), 3000);
    }
    document.getElementById("downloadData").addEventListener("click", () => {
        fetch('../../controller/UserC/export_data.php') // chemin vers ton script PHP
            .then(res => res.blob())
            .then(blob => {
                const url = URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = "mes_donnees.json"; // fichier téléchargé
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            })
            .catch(err => console.error("Erreur export données :", err));
    });

    function clearInputError(input) {
        if (!input || !input.parentElement) return;
        let container = input.parentElement.querySelector(".input-error");
        if (container) container.remove();
        input.classList.remove("error-input", "success-input");
    }

    // === Avatar ===
    const avatarInput = document.querySelector("input[name='avatar']");
    const avatarImg = document.getElementById("avatarPreview");
    avatarInput?.addEventListener("change", () => {
        const file = avatarInput.files[0];
        if (file) avatarImg.src = URL.createObjectURL(file);
    });
    document.getElementById("avatarForm")?.addEventListener("submit", e => {
        e.preventDefault();
        fetch("../../controller/UserC/update_avatar.php", { method: "POST", body: new FormData(e.target) })
            .then(res => res.json())
            .then(data => {
                clearInputError(avatarInput);
                if (data.errors?.avatar) createLabel(avatarInput, data.errors.avatar, false);
                else if (data.success) {
                    createLabel(avatarInput, "Avatar mis à jour !", true);
                    avatarImg.src = data.avatar + "?v=" + Date.now();
                }
            });
    });

    // === Profil pseudo/email ===
    document.getElementById("profileForm")?.addEventListener("submit", e => {
        e.preventDefault();
        const pseudo = e.target.querySelector("input[name='pseudo']");
        const email = e.target.querySelector("input[name='email']");
        clearInputError(pseudo); clearInputError(email);
        fetch("../../controller/UserC/update_profile.php", { method: "POST", body: new FormData(e.target) })
            .then(res => res.json())
            .then(data => {
                if (data.errors) {
                    if (data.errors.pseudo) createLabel(pseudo, data.errors.pseudo, false);
                    if (data.errors.email) createLabel(email, data.errors.email, false);
                } else if (data.success) {
                    createLabel(pseudo, "Profil mis à jour !", true);
                    createLabel(email, "Profil mis à jour !", true);
                }
            });
    });

    // === Mot de passe ===
    document.getElementById("passwordForm")?.addEventListener("submit", e => {
        e.preventDefault();
        const current = e.target.querySelector("#currentPassword");
        const psw = e.target.querySelector("#newPassword");
        const confirm = e.target.querySelector("#confirmPassword");
        clearInputError(current); clearInputError(psw); clearInputError(confirm);

        let valid = true;
        if (!current.value.trim()) { createLabel(current, "Mot de passe actuel requis", false); valid = false; }
        if (!psw.value.trim()) { createLabel(psw, "Nouveau mot de passe requis", false); valid = false; }
        if (psw.value !== confirm.value) { createLabel(confirm, "Les mots de passe ne correspondent pas", false); valid = false; }
        if (!valid) return;

        fetch("../../controller/UserC/update_password.php", { method: "POST", body: new FormData(e.target) })
            .then(res => res.json())
            .then(data => {
                if (data.errors) {
                    if (data.errors.currentPassword) createLabel(current, data.errors.currentPassword, false);
                    if (data.errors.newPassword) createLabel(psw, data.errors.newPassword, false);
                    if (data.errors.confirmPassword) createLabel(confirm, data.errors.confirmPassword, false);
                } else if (data.success) {
                    createLabel(psw, "Mot de passe modifié !", true);
                    e.target.reset();
                }
            });
    });

    // === Dropdown ===
    const avatarEl = document.getElementById('user-avatar');
    const dropdown = document.getElementById('user-dropdown');
    avatarEl.addEventListener('click', e => {
        e.stopPropagation();
        const isVisible = dropdown.style.opacity === '1';
        dropdown.style.opacity = isVisible ? '0' : '1';
        dropdown.style.visibility = isVisible ? 'hidden' : 'visible';
    });
    document.addEventListener('click', () => { dropdown.style.opacity = '0'; dropdown.style.visibility = 'hidden'; });

    // === Statut actif/inactif ===
    const statusForm = document.getElementById('statusForm');
    const statusSelect = document.getElementById('statusSelect');
    const statusBadge = document.getElementById('statusBadge');
    const statusText = document.getElementById('statusText');

    statusForm?.addEventListener('submit', e => {
        e.preventDefault();
        const status = statusSelect.value;
        fetch("../../controller/UserC/update_status_user.php", {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'status=' + encodeURIComponent(status)
        }).then(res => res.json()).then(data => {
            if (data.success) {
                statusBadge.style.background = (data.status === 'actif') ? '#4CAF50' : '#888';
                statusText.textContent = (data.status === 'actif') ? 'Actif' : 'Inactif';
            }
        });
    });

    // === Gestion Remember Me pour statut ===
    const remember = !!window.localStorage.getItem('rememberMe'); // ou variable côté serveur

    if (remember) {
        // Ping actif toutes les 30s
        setInterval(() => {
            fetch("../../controller/UserC/update_status_user.php", {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'status=actif'
            });
        }, 30000);
    }

    // === Galaxy canvas ===
    const canvas = document.getElementById('galaxyCanvas');
    const ctx = canvas.getContext('2d');
    canvas.width = innerWidth;
    canvas.height = innerHeight;
    const stars = Array.from({ length: 450 }, () => ({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        r: Math.random() * 1.7 + 0.4,
        a: Math.random(),
        s: Math.random() * 0.6 + 0.2
    }));

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        stars.forEach(s => {
            ctx.beginPath();
            ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255,255,255,${s.a})`;
            ctx.fill();
            s.a += (Math.random() - 0.5) * 0.04;
            s.a = Math.max(0.3, Math.min(1, s.a));
            s.x += s.s;
            if (s.x > canvas.width) s.x = 0;
        });
        requestAnimationFrame(animate);
    }

    animate();
    window.addEventListener('resize', () => { canvas.width = innerWidth; canvas.height = innerHeight; });
});
