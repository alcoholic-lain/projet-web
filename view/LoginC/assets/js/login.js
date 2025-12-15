document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("loginForm");
    const messageBox = document.getElementById("messageBox");
    const captchaQuestion = document.getElementById("captchaQuestion");
    const captchaAnswerInput = document.getElementById("captchaAnswer");
    const refreshCaptchaBtn = document.getElementById("refreshCaptcha");

    let captchaValue = 0;

    // === Fonction pour générer CAPTCHA visuel 🌟+🌑 ===
    function generateCaptcha() {
        const star = "🌟", moon = "🌑";
        const a = Math.floor(Math.random() * 5) + 1;
        const b = Math.floor(Math.random() * 5) + 1;
        captchaValue = a + b;
        captchaQuestion.textContent = `${star.repeat(a)} + ${moon.repeat(b)} = ?`;
        captchaAnswerInput.value = "";
    }

    generateCaptcha();

    refreshCaptchaBtn.addEventListener("click", generateCaptcha);

    // === Gestion du formulaire ===
    form.addEventListener("submit", function(e) {
        e.preventDefault();

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();
        const rememberMe = document.getElementById("rememberMe").checked;
        const captchaAnswer = parseInt(captchaAnswerInput.value.trim(), 10);

        if (!email || !password) {
            showMessage("Veuillez remplir tous les champs !", "error");
            return;
        }

        if (captchaAnswer !== captchaValue) {
            showMessage("❌ CAPTCHA incorrect.", "error");
            generateCaptcha();
            return;
        }

        // === reCAPTCHA v3 ===
        grecaptcha.ready(function() {
            grecaptcha.execute('6LcoLSMsAAAAAHVy43IaNYMSESN7Xc-8MYGty6Zd', {action: 'login'}).then(function(token) {

                fetch("../../controller/UserC/LoginController.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&rememberMe=${rememberMe ? 1 : 0}&recaptcha_token=${token}`
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showMessage(data.message, "success");
                            setTimeout(() => {
                                if (data.role_id == 1) {
                                    window.location.href = "../Admin/index.php";
                                } else {
                                    window.location.href = "../Client/F_index.php";
                                }
                            }, 1000);
                        } else {
                            showMessage(data.message || "Email ou mot de passe incorrect.", "error");
                            generateCaptcha();
                        }
                    })
                    .catch(err => {
                        console.error("Erreur fetch:", err);
                        showMessage("Erreur serveur, veuillez réessayer.", "error");
                        generateCaptcha();
                    });

            });
        });
    });

    function showMessage(msg, type) {
        messageBox.textContent = msg;
        messageBox.className = `message-box ${type}`;
        messageBox.style.display = "block";
        if (type === "success") setTimeout(() => { messageBox.style.display = "none"; }, 5000);
    }

    // === Galaxy canvas ===
    const canvas = document.getElementById('galaxyCanvas');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const stars = Array.from({length: 450}, () => ({
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

    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
});
