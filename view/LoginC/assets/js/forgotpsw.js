// assets/js/forgotpsw.js – VERSION FINALE 100% FONCTIONNELLE

// ======================
// 1. CONTRÔLE DU FORMULAIRE + MESSAGES
// ======================
const form = document.getElementById("forgotForm");
const emailInput = form?.querySelector("input[name='email']");
const messageDiv = document.getElementById("message");

if (form && emailInput && messageDiv) {
    // Contrôle en temps réel (dès que l'utilisateur tape)
    emailInput.addEventListener("input", function () {
        const email = this.value.trim();
        if (email === "") {
            showMessage("Entre ton adresse email", "#ff4444");
        } else if (!/^\S+@\S+\.\S+$/.test(email)) {
            showMessage("Cet email n’est pas valide", "#ff4444");
        } else {
            clearMessage();
        }
    });

    // Soumission du formulaire
    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const email = emailInput.value.trim();

        if (email === "") {
            showMessage("L’email est obligatoire", "#ff4444");
            return;
        }
        if (!/^\S+@\S+\.\S+$/.test(email)) {
            showMessage("Entre un email valide (ex: toi@gmail.com)", "#ff4444");
            return;
        }

        showMessage("Envoi en cours...", "#8b5cf6");

        const fd = new FormData(this);
        try {
            const r = await fetch("../../controller/UserC/ForgotPasswordController.php", {
                method: "POST",
                body: fd
            });
            const d = await r.json();

            if (d.success) {
                showMessage(d.message || "Lien envoyé ! Vérifie ta boîte mail", "#00ff88");
                form.reset();
                clearMessage(4000); // efface le message après 4 secondes
            } else {
                showMessage(d.message || "Une erreur est survenue", "#ff4444");
            }
        } catch (err) {
            showMessage("Erreur réseau – vérifie ta connexion", "#ff4444");
        }
    });
}




















// Fonctions utilitaires pour les messages
function showMessage(text, color) {
    if (messageDiv) {
        messageDiv.textContent = text;
        messageDiv.style.color = color;
        messageDiv.style.opacity = "1";
        messageDiv.style.fontWeight = "bold";
        messageDiv.style.marginTop = "15px";
    }
}

function clearMessage(timeout = 0) {
    if (timeout > 0) {
        setTimeout(() => {
            if (messageDiv) {
                messageDiv.style.opacity = "0";
                setTimeout(() => { messageDiv.textContent = ""; }, 300);
            }
        }, timeout);
    } else if (messageDiv) {
        messageDiv.textContent = "";
    }
}

// ======================
// 2. ANIMATION GALAXY (inchangée et magnifique)
// ======================
const canvas = document.createElement("canvas");
canvas.id = "galaxyCanvas";
document.body.prepend(canvas);
const ctx = canvas.getContext("2d");
let w, h;

function resize() {
    w = canvas.width = window.innerWidth;
    h = canvas.height = window.innerHeight;
}
resize();
window.addEventListener("resize", resize);

const stars = Array.from({ length: 600 }, () => ({
    x: Math.random() * w,
    y: Math.random() * h,
    r: Math.random() * 1.8 + 0.5,
    a: Math.random() * 0.8 + 0.2,
    s: Math.random() * 0.8 + 0.2,
    t: Math.random() * 0.03 + 0.01
}));

function animate() {
    ctx.clearRect(0, 0, w, h);
    stars.forEach(s => {
        s.a += (Math.random() - 0.5) * s.t;
        s.a = Math.max(0.2, Math.min(1, s.a));
        s.x += s.s;
        if (s.x > w) s.x = 0;

        ctx.beginPath();
        ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(255,255,255,${s.a})`;
        ctx.fill();
    });
    requestAnimationFrame(animate);
}
animate();








