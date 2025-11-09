  document.getElementById("registerForm").addEventListener("submit", function(e) {
    e.preventDefault();

    // Récupération des champs
    const pseudo = document.getElementById("pseudo").value.trim();
    const email = document.getElementById("email").value.trim();
    const psw = document.getElementById("psw").value.trim();
    const planet = document.getElementById("planet").value.trim();

    // Supprimer tous les messages d'erreur/succès existants
    document.querySelectorAll(".error-label, .success-label").forEach(l => l.remove());

    // Création d’un label de message juste après un input
    function createLabel(input, message, isValid) {
      const label = document.createElement("label");
      label.textContent = message;
      label.style.display = "block";
      label.style.marginTop = "4px";
      label.style.fontWeight = "bold";
      if(isValid){
        label.style.color = "green";
        label.className = "success-label"}
      else{
        label.style.color = "red";
        label.className = "error-label";
      }
      input.insertAdjacentElement("afterend", label);
    }

    let isFormValid = true;

    // --- Validation du pseudo ---
    const pseudoInput = document.getElementById("pseudo");
    if (pseudo === "") {
      createLabel(pseudoInput, " Veuillez saisir votre pseudo", false);
      isFormValid = false;
      pseudo.focus();
    } else {
      createLabel(pseudoInput, " Pseudo valide", true);
    }

  // --- Validation de l’email ---
  const emailInput = document.getElementById("email");

  if (email === "") {
    createLabel(emailInput, " Veuillez saisir votre email", false);
    isFormValid = false;
    emailInput.focus();
  } 
  else if (email.indexOf("@") === -1 || email.indexOf(".") === -1) {
    createLabel(emailInput, " L'adresse email doit contenir '@' et '.'", false);
    isFormValid = false;
    emailInput.focus();
  } 
  else if (email.indexOf("@") > email.lastIndexOf(".")) {
    createLabel(emailInput, " Le symbole '@' doit venir avant le '.'", false);
    isFormValid = false;
    emailInput.focus();
  } 
  else {
    createLabel(emailInput, "Email valide", true);
  }


    // --- Validation du mot de passe ---
    const pswInput = document.getElementById("psw");
    if (psw === "") {
      createLabel(pswInput, " Veuillez saisir un mot de passe", false);
      isFormValid = false;
      pswInput.focus();
    } else if (psw.length < 6) {
      createLabel(pswInput, " Le mot de passe doit contenir au moins 6 caractères", false);
      isFormValid = false;
      pswInput.focus();
    } else {
      createLabel(pswInput, " Mot de passe valide", true);
    }

    // --- Validation de la planète ---
    const planetInput = document.getElementById("planet");
    if (planet === "") {
      createLabel(planetInput, " Veuillez choisir une planète", false);
      isFormValid = false;
      planetInput.focus();
    } else {
      createLabel(planetInput, " Planète sélectionnée", true);
    }
    //envoi si form valide
    if(isFormValid){
  fetch("../controller/RegisterController.php", {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `pseudo=${encodeURIComponent(pseudo)}&email=${encodeURIComponent(email)}&psw=${encodeURIComponent(psw)}&planet=${encodeURIComponent(planet)}`
  })
  .then(res => res.text())
  .then(data => alert(data));
}



  });
