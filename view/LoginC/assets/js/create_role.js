document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('createRoleForm');
    const messageBox = document.getElementById('messageBox');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const nom = form.nom.value.trim();
        const description = form.description.value.trim();

        if (!nom) {
            messageBox.textContent = '❌ Le nom du rôle est obligatoire.';
            messageBox.style.color = 'red';
            return;
        }

        const formData = new FormData();
        formData.append('nom', nom);
        formData.append('description', description);
        formData.append('ajax', 1); // signal AJAX

        fetch('../../controller/UserC/create_role.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) throw new Error('Réponse réseau non OK');
                return response.json(); // parse JSON
            })
            .then(data => {
                messageBox.textContent = (data.success ? '✅ ' : '❌ ') + data.message;
                messageBox.style.color = data.success ? 'green' : 'red';

                if (data.success) {
                    form.reset();
                    setTimeout(() => {
                        window.location.href = '../../../BackEnd/UserB/dashboard.php';
                    }, 2000); // 2 secondes de délai
                }
            })
            .catch(error => {
                messageBox.textContent = '❌ Erreur serveur : ' + error.message;
                messageBox.style.color = 'red';
            });
    });
});
