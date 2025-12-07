// view/F/assets/js/chat.js

document.addEventListener("DOMContentLoaded", function () {
    // Auto-scroll messages
    const msgBox = document.getElementById("messages");
    if (msgBox) msgBox.scrollTop = msgBox.scrollHeight;

    // Edit message toggle
    document.querySelectorAll(".message .edit-message").forEach(btn => {
        btn.addEventListener("click", () => {
            const msg = btn.closest(".message");
            msg?.querySelector(".edit-message-form")?.classList.remove("d-none");
            msg?.querySelector(".text")?.classList.add("d-none");
        });
    });

    document.querySelectorAll(".message .cancel-edit").forEach(btn => {
        btn.addEventListener("click", () => {
            const msg = btn.closest(".message");
            msg?.querySelector(".edit-message-form")?.classList.add("d-none");
            msg?.querySelector(".text")?.classList.remove("d-none");
        });
    });

    // ─────── THEME TOGGLE ───────
    const body = document.body;
    const toggle = document.getElementById("themeToggle");

    // Load saved theme (default = dark)
    if (localStorage.getItem("chatTheme") === "light") {
        body.classList.add("theme-light");
        body.classList.remove("theme-dark");
        if (toggle) toggle.innerHTML = "Moon";
    } else {
        body.classList.add("theme-dark");
        body.classList.remove("theme-light");
        if (toggle) toggle.innerHTML = "Sun";
    }

    // Click to toggle
    if (toggle) {
        toggle.addEventListener("click", () => {
            if (body.classList.contains("theme-dark")) {
                body.classList.replace("theme-dark", "theme-light");
                localStorage.setItem("chatTheme", "light");
                toggle.innerHTML = "Moon";
            } else {
                body.classList.replace("theme-light", "theme-dark");
                localStorage.setItem("chatTheme", "dark");
                toggle.innerHTML = "Sun";
            }
        });
    }

    // ─────── GALAXY STARS ANIMATION ───────
    const canvas = document.getElementById("galaxyCanvas");
    if (!canvas) return;
    const ctx = canvas.getContext("2d");
    let w, h;

    const resize = () => {
        w = canvas.width = window.innerWidth;
        h = canvas.height = window.innerHeight;
    };
    resize();
    window.addEventListener("resize", resize);

    const stars = [];
    for (let i = 0; i < 400; i++) {
        stars.push({
            x: Math.random() * w,
            y: Math.random() * h,
            radius: Math.random() * 2,
            alpha: Math.random() * 0.8 + 0.2
        });
    }

    const animate = () => {
        ctx.clearRect(0, 0, w, h);
        stars.forEach(s => {
            ctx.beginPath();
            ctx.arc(s.x, s.y, s.radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255, 255, 255, ${s.alpha})`;
            ctx.fill();

            s.alpha += (Math.random() - 0.5) * 0.05;
            s.alpha = Math.max(0.2, Math.min(1, s.alpha));
        });
        requestAnimationFrame(animate);
    };
    animate();
});