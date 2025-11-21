/* ==========================================================
   GALAXY 3D FAKE (PARALLAX + PARTICLES EN PROFONDEUR)
   ========================================================== */

function initGalaxy() {
    const existing = document.getElementById('galaxyCanvas');
    if (existing) return; // si déjà créé

    const canvas = document.createElement('canvas');
    canvas.id = 'galaxyCanvas';
    document.body.appendChild(canvas);

    const ctx = canvas.getContext('2d');
    let width = window.innerWidth;
    let height = window.innerHeight;
    canvas.width = width;
    canvas.height = height;

    // Particules "3D"
    const STAR_COUNT = 260;
    const stars = [];
    const colors = ['#ff7f2a', '#ff9d5c', '#ff2a7f', '#ffd0a1'];

    for (let i = 0; i < STAR_COUNT; i++) {
        stars.push({
            x: Math.random() * width,
            y: Math.random() * height,
            z: Math.random() * 1.2 + 0.2, // profondeur
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
        targetMouseX = (e.clientX - centerX) / centerX; // -1 à 1
        targetMouseY = (e.clientY - centerY) / centerY;
    });

    window.addEventListener('resize', () => {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
    });

    function render() {
        // petit easing pour un mouvement smooth
        mouseX += (targetMouseX - mouseX) * 0.06;
        mouseY += (targetMouseY - mouseY) * 0.06;

        // fond léger, on garde un soupçon de trail
        ctx.fillStyle = 'rgba(5, 8, 20, 0.78)';
        ctx.fillRect(0, 0, width, height);

        for (const s of stars) {
            // déplacement "vers le bas" pour donner l'impression d'un flux
            s.y += s.speed * (1.2 - s.z);
            if (s.y > height + 20) {
                s.y = -20;
                s.x = Math.random() * width;
                s.z = Math.random() * 1.2 + 0.2;
            }

            // parallax en fonction de la profondeur
            const parallaxX = mouseX * (30 * s.z);
            const parallaxY = mouseY * (18 * s.z);

            const drawX = s.x + parallaxX;
            const drawY = s.y + parallaxY;

            const size = s.radius * (1.5 - s.z); // plus proches = plus gros

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

/* ==========================================================
   VALIDATION FORMULAIRE
   ========================================================== */

function setupFormValidation() {
    const form = document.getElementById('innovationForm');
    if (!form) return;

    const titreInput = document.getElementById('titre');
    const catSelect = document.getElementById('categorie_id');
    const descTextarea = document.getElementById('description');
    const errorBox = document.getElementById('error-box');
    const validatedFlag = document.getElementById('validated');

    form.addEventListener('submit', function (e) {
        let errors = [];

        // reset styles
        [titreInput, catSelect, descTextarea].forEach(el => {
            el.classList.remove('cs-invalid');
        });

        const titre = titreInput.value.trim();
        const cat = catSelect.value.trim();
        const desc = descTextarea.value.trim();

        if (titre === '') {
            errors.push('Le titre de l’innovation est obligatoire.');
            titreInput.classList.add('cs-invalid');
        }

        if (cat === '') {
            errors.push('Veuillez sélectionner une catégorie.');
            catSelect.classList.add('cs-invalid');
        }

        if (desc === '') {
            errors.push('La description est obligatoire.');
            descTextarea.classList.add('cs-invalid');
        }

        if (errors.length > 0) {
            e.preventDefault();
            errorBox.innerHTML = errors.join('<br>');
            errorBox.classList.remove('hidden');
            validatedFlag.value = '0';
        } else {
            errorBox.classList.add('hidden');
            validatedFlag.value = '1';
        }
    });
}

/* ==========================================================
   INIT GLOBAL
   ========================================================== */

document.addEventListener('DOMContentLoaded', () => {
    initGalaxy();          // fond galaxie animé
    setupFormValidation(); // contrôle de saisie
});
