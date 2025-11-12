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
        fetch("../../controller/RegisterController.php", {
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
