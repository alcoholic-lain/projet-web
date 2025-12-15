document.getElementById("registerForm").addEventListener("submit", function(e) {
    e.preventDefault();

    var pseudo = document.getElementById("pseudo").value.trim();
    var email = document.getElementById("email").value.trim();
    var psw = document.getElementById("psw").value.trim();
    var planet = document.getElementById("planet").value.trim();

    // Supprimer tous les messages existants
    document.querySelectorAll(".error-label, .success-label").forEach(l => l.remove());
    const globalMsg = document.getElementById("globalMessage");
    globalMsg.textContent = "";

    function createLabel(input, message, isValid) {
        const label = document.createElement("label");
        label.textContent = message;
        label.style.display = "block";
        label.style.marginTop = "4px";
        label.style.fontWeight = "bold";
        label.style.color = isValid ? "green" : "red";
        label.className = isValid ? "success-label" : "error-label";
        input.insertAdjacentElement("afterend", label);
    }

    let isFormValid = true;

    // Validation pseudo
    const pseudoInput = document.getElementById("pseudo");
    if(pseudo === "") {
        createLabel(pseudoInput, "Veuillez saisir votre pseudo", false);
        isFormValid=false; }
    else { createLabel(pseudoInput, "Pseudo valide", true); }

    // Validation email
    const emailInput = document.getElementById("email");
    if(email === "") {
        createLabel(emailInput, "Veuillez saisir votre email", false);
        isFormValid=false; }
    else if(email.indexOf("@") === -1 || email.indexOf(".") === -1)
    {
        createLabel(emailInput, "L'email doit contenir '@' et '.'", false);
        isFormValid=false; }
    else if(email.indexOf("@") > email.lastIndexOf("."))
    {
        createLabel(emailInput, "'@' doit venir avant '.'", false);
        isFormValid=false; }
    else { createLabel(emailInput, "Email valide", true); }

    // Validation mot de passe
    const pswInput = document.getElementById("psw");
    if(psw === "") {
        createLabel(pswInput, "Veuillez saisir un mot de passe", false);
        isFormValid=false; }
    else if(psw.length < 6) {
        createLabel(pswInput, "Le mot de passe doit contenir au moins 6 caractères", false);
        isFormValid=false; }
    else { createLabel(pswInput, "Mot de passe valide", true); }

    // Validation planète
    const planetInput = document.getElementById("planet");
    if(planet === "") {
        createLabel(planetInput, "Veuillez choisir une planète", false);
        isFormValid=false; }
    else { createLabel(planetInput, "Planète sélectionnée", true); }

    // Envoi si valide
    if(isFormValid){
        fetch("../../controller/UserC/RegisterController.php", {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: `pseudo=${encodeURIComponent(pseudo)}&email=${encodeURIComponent(email)}&psw=${encodeURIComponent(psw)}&planet=${encodeURIComponent(planet)}`
        })
            .then(res => res.json())
            .then(data => {
                globalMsg.textContent = data.message;
                if (data.success) {
                    globalMsg.style.color = "green";
                } else {
                    globalMsg.style.color = "red";
                    document.querySelectorAll(" .success-label").forEach(l => l.remove());

                }



                if(data.success){
                    setTimeout(() => { window.location.href = "login.html"; }, 2000);
                }
            })
            .catch(err => {
                globalMsg.textContent = "❌ Erreur serveur : " + err;
                globalMsg.style.color = "red";

            });
    }
});


const canvas = document.createElement('canvas');
canvas.id = "galaxyCanvas";
document.body.appendChild(canvas);
const ctx = canvas.getContext('2d');
canvas.width = innerWidth;
canvas.height = innerHeight;

const stars = Array.from({length:450},()=>({
    x:Math.random()*canvas.width,
    y:Math.random()*canvas.height,
    r:Math.random()*1.7+0.4,
    a:Math.random(),
    s:Math.random()*0.6+0.2
}));

function animate(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    stars.forEach(s=>{
        ctx.beginPath();ctx.arc(s.x,s.y,s.r,0,Math.PI*2);
        ctx.fillStyle=`rgba(255,255,255,${s.a})`;ctx.fill();
        s.a += (Math.random()-0.5)*0.04;
        s.a=Math.max(0.3,Math.min(1,s.a));
        s.x += s.s;
        if(s.x>canvas.width) s.x=0;
    });
    requestAnimationFrame(animate);
}
animate();

window.addEventListener('resize',()=>{
    canvas.width=innerWidth;
    canvas.height=innerHeight;
});