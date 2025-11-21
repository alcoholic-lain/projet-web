/* ============================================================
   USER.JS — Galaxy Background + Solar Flare Hero compatible
============================================================ */

document.addEventListener("DOMContentLoaded", () => {

    /* ========================================================
       DM POPUP
    ========================================================= */
    const dmBtn = document.getElementById("dm-btn");
    const popup = document.getElementById("dm-popup");

    if (dmBtn && popup) {
        dmBtn.addEventListener("click", () => {
            popup.style.display = popup.style.display === "block" ? "none" : "block";
        });
    }

    /* ========================================================
       GALAXY BACKGROUND (stars + shooting stars + tiny planet)
    ========================================================= */

    const canvas = document.getElementById("galaxyCanvas");
    if (!canvas) return;

    const ctx = canvas.getContext("2d");

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    resizeCanvas();
    window.addEventListener("resize", resizeCanvas);

    /* ---- Stars ---- */
    const stars = [];
    const STAR_COUNT = 260;

    for (let i = 0; i < STAR_COUNT; i++) {
        stars.push({
            x: Math.random(),
            y: Math.random(),
            r: Math.random() * 1.5 + 0.4,
            speed: Math.random() * 0.0008 + 0.0002,
            twinkle: Math.random() * Math.PI * 2
        });
    }

    /* ---- Shooting stars ---- */
    const shooting = [];

    function addShootingStar() {
        shooting.push({
            x: Math.random() * canvas.width,
            y: -20,
            len: Math.random() * 100 + 80,
            speed: Math.random() * 7 + 4,
            size: Math.random() * 1.7 + 0.4
        });
    }
    setInterval(addShootingStar, 2800);

    /* ---- Tiny planet (bottom-right) ---- */
    const planet = {
        baseX: 0.80,
        baseY: 0.88,
        radius: 30,
        angle: 0,
        amp: 0.015,
        speed: 0.0006
    };

    /* ---- Parallax ---- */
    let parallaxX = 0, parallaxY = 0;

    document.addEventListener("mousemove", (e) => {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        parallaxX = (e.clientX - cx) * 0.015;
        parallaxY = (e.clientY - cy) * 0.015;
    });

    /* ========================================================
       DRAW LOOP
    ========================================================= */
    function drawGalaxy() {
        const w = canvas.width;
        const h = canvas.height;

        ctx.clearRect(0, 0, w, h);

        ctx.save();
        ctx.translate(parallaxX, parallaxY);

        /* Stars */
        stars.forEach(star => {
            star.y -= star.speed;
            if (star.y < 0) star.y = 1;

            star.twinkle += 0.03;
            const alpha = 0.35 + 0.3 * Math.sin(star.twinkle);

            ctx.beginPath();
            ctx.arc(star.x * w, star.y * h, star.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255,255,255,${alpha})`;
            ctx.fill();
        });

        /* Shooting stars */
        shooting.forEach((s, i) => {
            ctx.beginPath();
            ctx.moveTo(s.x, s.y);
            ctx.lineTo(s.x - s.len, s.y + s.len / 3);
            ctx.strokeStyle = "rgba(255,255,255,0.7)";
            ctx.lineWidth = s.size;
            ctx.stroke();

            s.x += s.speed;
            s.y += s.speed * 0.4;

            if (s.x > w + 50 || s.y > h + 50) shooting.splice(i, 1);
        });

        /* Tiny planet */
        planet.angle += planet.speed;
        const px = (planet.baseX + Math.sin(planet.angle) * planet.amp) * w;
        const py = (planet.baseY + Math.cos(planet.angle) * planet.amp) * h;

        const gRadius = planet.radius * 2.2;
        const gradient = ctx.createRadialGradient(
            px, py, planet.radius * 0.2,
            px, py, gRadius
        );
        gradient.addColorStop(0, "rgba(255,160,220,0.95)");
        gradient.addColorStop(0.4, "rgba(255,140,200,0.6)");
        gradient.addColorStop(1, "rgba(0,0,0,0)");

        ctx.beginPath();
        ctx.arc(px, py, gRadius, 0, Math.PI * 2);
        ctx.fillStyle = gradient;
        ctx.fill();

        ctx.beginPath();
        ctx.arc(px, py, planet.radius, 0, Math.PI * 2);
        ctx.fillStyle = "rgba(255, 190, 230, 1)";
        ctx.fill();

        ctx.restore();

        requestAnimationFrame(drawGalaxy);
    }

    drawGalaxy();
});
