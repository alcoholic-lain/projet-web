document.addEventListener('DOMContentLoaded', () => {
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const newPasswordMsg = document.getElementById('newPasswordMsg');
    const confirmPasswordMsg = document.getElementById('confirmPasswordMsg');
    const form = document.querySelector('form');

    // Fonction pour valider le mot de passe
    function validatePassword() {
        const val = newPassword.value;
        let errors = [];

        if (val.length < 8) errors.push("8 caractères minimum");
        if (!/[A-Z]/.test(val)) errors.push("1 majuscule requise");
        if (!/[a-z]/.test(val)) errors.push("1 minuscule requise");
        if (!/[0-9]/.test(val)) errors.push("1 chiffre requis");
        if (!/[^A-Za-z0-9]/.test(val)) errors.push("1 symbole requis (!@#$%^&*...)");

        newPasswordMsg.innerHTML = errors.join('<br>');
        return errors.length === 0;
    }

    // Fonction pour valider la confirmation
    function validateConfirm() {
        const val = confirmPassword.value;
        if (val !== newPassword.value) {
            confirmPasswordMsg.textContent = "Les mots de passe ne correspondent pas";
            return false;
        } else {
            confirmPasswordMsg.textContent = "";
            return true;
        }
    }

    // Événements
    newPassword.addEventListener('input', () => {
        validatePassword();
        validateConfirm();
    });

    confirmPassword.addEventListener('input', validateConfirm);

    // Bloquer le submit si erreurs
    form.addEventListener('submit', (e) => {
        const passOk = validatePassword();
        const confirmOk = validateConfirm();
        if (!passOk || !confirmOk) e.preventDefault();
    });
});
