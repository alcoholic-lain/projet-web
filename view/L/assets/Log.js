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