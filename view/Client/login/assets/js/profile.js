document.addEventListener("DOMContentLoaded", () => {

    function createLabel(input, message, isSuccess=false){
        if(!input || !input.parentElement) return;

        let container = input.parentElement.querySelector(".input-error");
        if(!container){
            container = document.createElement("div");
            container.className = "input-error";
            input.parentElement.appendChild(container);
        }
        container.innerText = message;
        container.style.color = isSuccess ? "green" : "red";
        input.classList.remove("error-input", "success-input");
        input.classList.add(isSuccess ? "success-input" : "error-input");

        // Timeout pour les messages de succès
        if(isSuccess){
            setTimeout(() => {
                clearInputError(input);
            }, 3000); // 3 secondes
        }
    }

    function clearInputError(input){
        if(!input || !input.parentElement) return;

        let container = input.parentElement.querySelector(".input-error");
        if(container) container.remove();
        input.classList.remove("error-input");
        input.classList.remove("success-input");
    }

    // Avatar preview + upload
    const avatarInput = document.querySelector("input[name='avatar']");
    const avatarImg = document.getElementById("avatarPreview");

    if(avatarInput && avatarImg){
        avatarInput.addEventListener("change", () => {
            const file = avatarInput.files[0];
            if(file) avatarImg.src = URL.createObjectURL(file);
        });

        const avatarForm = document.getElementById("avatarForm");
        if(avatarForm){
            avatarForm.addEventListener("submit", function(e){
                e.preventDefault();
                const formData = new FormData(this);
                fetch("http://localhost/projet-web/controller/components/login/update_avatar.php", {method: "POST", body: formData})
                    .then(res => res.json())
                    .then(data => {
                        clearInputError(avatarInput);
                        if(data.errors && data.errors.avatar){
                            createLabel(avatarInput, data.errors.avatar, false);
                        } else if(data.success){
                            createLabel(avatarInput, "Avatar mis à jour !", true);
                            avatarImg.src = data.avatar + "?v=" + Date.now();
                            localStorage.setItem("user_avatar", data.avatar);

                        }
                    })
                    .catch(err => console.error("Erreur upload avatar:", err));
            });
        }
    }

    // Form profil (pseudo + email)
    const profileForm = document.getElementById("profileForm");
    if(profileForm){
        profileForm.addEventListener("submit", function(e){
            e.preventDefault();
            const pseudo = profileForm.querySelector("input[name='pseudo']");
            const email = profileForm.querySelector("input[name='email']");

            clearInputError(pseudo);
            clearInputError(email);

            fetch("http://localhost/projet-web/controller/components/login/update_profile.php", {method: "POST", body: new FormData(profileForm)})
                .then(res => res.json())
                .then(data => {
                    clearInputError(pseudo);
                    clearInputError(email);

                    if(data.errors){
                        if(data.errors.pseudo) createLabel(pseudo,data.errors.pseudo,false);
                        if(data.errors.email) createLabel(email,data.errors.email,false);
                    } else if(data.success){
                        createLabel(pseudo,"Profil mis à jour !",true);
                        createLabel(email,"Profil mis à jour !",true);
                    }
                });
        });
    }

    // Form mot de passe
    const passwordForm = document.getElementById("passwordForm");
    if(passwordForm){
        passwordForm.addEventListener("submit", function(e){
            e.preventDefault();

            const current = passwordForm.querySelector("#currentPassword");
            const pswInput = passwordForm.querySelector("#newPassword");
            const confirmInput = passwordForm.querySelector("#confirmPassword");

            clearInputError(current);
            clearInputError(pswInput);
            clearInputError(confirmInput);

            let valid = true;
            const psw = pswInput.value.trim();
            const confirm = confirmInput.value.trim();

            if(current.value.trim() === "") { createLabel(current,"Veuillez saisir le mot de passe actuel",false); valid=false; }
            if(psw === "") { createLabel(pswInput,"Veuillez saisir un mot de passe",false); valid=false; }
            else if(psw.length<8) { createLabel(pswInput,"Le mot de passe doit contenir au moins 8 caractères",false); valid=false; }
            else if(!/[A-Z]/.test(psw)) { createLabel(pswInput,"Le mot de passe doit contenir au moins une majuscule",false); valid=false; }
            else if(!/[a-z]/.test(psw)) { createLabel(pswInput,"Le mot de passe doit contenir au moins une minuscule",false); valid=false; }
            else if(!/[0-9]/.test(psw)) { createLabel(pswInput,"Le mot de passe doit contenir au moins un chiffre",false); valid=false; }
            else if(!/[!@#$%^&*()_\-+=<>?]/.test(psw)) { createLabel(pswInput,"Le mot de passe doit contenir au moins un symbole spécial (!@#$%^&*)",false); valid=false; }

            if(psw !== confirm) { createLabel(confirmInput,"Les mots de passe ne correspondent pas",false); valid=false; }

            if(!valid) return;

            fetch("http://localhost/projet-web/controller/components/login/update_password.php",{method:"POST", body:new FormData(passwordForm)})
                .then(res => res.json())
                .then(data => {
                    clearInputError(current);
                    clearInputError(pswInput);
                    clearInputError(confirmInput);

                    if(data.errors){
                        if(data.errors.currentPassword) createLabel(current,data.errors.currentPassword,false);
                        if(data.errors.newPassword) createLabel(pswInput,data.errors.newPassword,false);
                        if(data.errors.confirmPassword) createLabel(confirmInput,data.errors.confirmPassword,false);
                    } else if (data.success) {
                        createLabel(avatarInput, "Avatar mis à jour !", true);
                        avatarImg.src = data.avatar + "?v=" + Date.now();

                        // ⭐ Enregistrer l’avatar pour le front Space
                        localStorage.setItem("user_avatar", data.avatar);
                    }

                });
        });
    }

});
