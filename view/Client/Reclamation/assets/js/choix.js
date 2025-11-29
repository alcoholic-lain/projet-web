/********************************
 *  SÉLECTION DES IMAGES
 *********************************/
let selectedProblemType = '';

document.querySelectorAll('.img-box').forEach(box => {
    box.addEventListener('click', () => {
        const type = box.getAttribute('data-type');

        document.querySelectorAll('.img-box').forEach(b => b.classList.remove('selected'));

        if (selectedProblemType === type) {
            selectedProblemType = '';
        } else {
            selectedProblemType = type;
            box.classList.add('selected');
        }

        document.getElementById('sujet_type').value = selectedProblemType;
        validateForm();
    });
});

/********************************
 *  VALIDATION FORM
 *********************************/
const form = document.getElementById('contactForm');
const submitBtn = document.querySelector('.btn-submit');

submitBtn.disabled = true;
submitBtn.style.opacity = "0.6";

function validateForm() {
    const lang = document.getElementById('language').value;

    const isValid = selectedProblemType && lang;

    submitBtn.disabled = !isValid;
    submitBtn.style.opacity = isValid ? "1" : "0.6";

    return isValid;
}

/********************************
 *  REDIRECTION
 *********************************/
form.addEventListener('submit', e => {
    e.preventDefault();
    if (!validateForm()) return;

    const type = selectedProblemType.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    let page = "";

    switch(type) {
        case "contenu incorrect": page = "add-CI.php"; break;
        case "probleme technique": page = "add-PT.php"; break;
        case "probleme de securite": page = "add-PS.php"; break;
        case "compte bloque": page = "add-CB.php"; break;
    }

    const lang = document.getElementById('language').value;
    const folder = lang === "fr" ? "FR" : (lang === "en" ? "ANG" : "AR");

    window.location.href = `../src/${folder}/${page}`;
});

/********************************
 *  FOND GALAXY ANIMÉ 3D
 *********************************/
/* ============================
   GALAXY BACKGROUND — SAME AS CATEGORIES
   ============================ */

function initGalaxy() {
    const existing = document.getElementById('galaxyCanvas');
    if (existing) return;

    const canvas = document.createElement('canvas');
    canvas.id = 'galaxyCanvas';
    document.body.appendChild(canvas);

    const ctx = canvas.getContext('2d');
    let width = window.innerWidth;
    let height = window.innerHeight;
    canvas.width = width;
    canvas.height = height;

    const STAR_COUNT = 260;
    const stars = [];
    const colors = ['#ff7f2a', '#ff9d5c', '#ff2a7f', '#ffd0a1'];

    for (let i = 0; i < STAR_COUNT; i++) {
        stars.push({
            x: Math.random() * width,
            y: Math.random() * height,
            z: Math.random() * 1.2 + 0.2,
            radius: Math.random() * 1.6 + 0.4,
            speed: Math.random() * 0.35 + 0.05,
            color: colors[Math.floor(Math.random() * colors.length)]
        });
    }

    let mouseX = 0;
    let mouseY = 0;
    let targetMouseX = 0;
    let targetMouseY = 0;

    window.addEventListener('mousemove', (e) => {
        const centerX = width / 2;
        const centerY = height / 2;
        targetMouseX = (e.clientX - centerX) / centerX;
        targetMouseY = (e.clientY - centerY) / centerY;
    });

    window.addEventListener('resize', () => {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
    });

    function render() {
        mouseX += (targetMouseX - mouseX) * 0.06;
        mouseY += (targetMouseY - mouseY) * 0.06;

        ctx.fillStyle = 'rgba(5, 8, 20, 0.78)';
        ctx.fillRect(0, 0, width, height);

        for (const s of stars) {
            s.y += s.speed * (1.2 - s.z);
            if (s.y > height + 20) {
                s.y = -20;
                s.x = Math.random() * width;
                s.z = Math.random() * 1.2 + 0.2;
            }

            const parallaxX = mouseX * (30 * s.z);
            const parallaxY = mouseY * (18 * s.z);

            const drawX = s.x + parallaxX;
            const drawY = s.y + parallaxY;

            const size = s.radius * (1.5 - s.z);

            const gradient = ctx.createRadialGradient(
                drawX, drawY, 0,
                drawX, drawY, size * 3
            );
            gradient.addColorStop(0, s.color);
            gradient.addColorStop(0.45, s.color);
            gradient.addColorStop(1, 'rgba(0,0,0,0)');

            ctx.fillStyle = gradient;
            ctx.beginPath();
            ctx.arc(drawX, drawY, size * 2.3, 0, Math.PI * 2);
            ctx.fill();
        }

        requestAnimationFrame(render);
    }

    render();
}
document.addEventListener("DOMContentLoaded", () => {

    const select = document.querySelector(".custom-select");
    const trigger = select.querySelector(".custom-select-trigger");
    const options = select.querySelector(".custom-options");
    const optionItems = select.querySelectorAll(".custom-option");
    const hiddenInput = document.getElementById("language");
    const selectedText = document.getElementById("selected-option");

    trigger.addEventListener("click", () => {
        select.classList.toggle("open");
        options.style.display = select.classList.contains("open") ? "block" : "none";
    });

    optionItems.forEach(opt => {
        opt.addEventListener("click", () => {
            const value = opt.getAttribute("data-value");

            // Set hidden input
            hiddenInput.value = value;

            // Update display text
            selectedText.textContent = opt.textContent;

            // Highlight
            optionItems.forEach(o => o.classList.remove("selected"));
            opt.classList.add("selected");

            // Close dropdown
            select.classList.remove("open");
            options.style.display = "none";
        });
    });

    // Fermer en cliquant dehors
    document.addEventListener("click", (e) => {
        if (!select.contains(e.target)) {
            select.classList.remove("open");
            options.style.display = "none";
        }
    });

});

document.addEventListener("DOMContentLoaded", initGalaxy);
