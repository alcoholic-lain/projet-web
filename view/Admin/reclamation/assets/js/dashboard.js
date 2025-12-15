document.addEventListener("DOMContentLoaded", () => {

    // Fonction utilitaire pour créer un chart
    function createLineChart(ctx, label, labels, values, colorBorder, colorBg) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: values,
                    borderColor: colorBorder,
                    backgroundColor: colorBg,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // --- Connexions Chart ---
    fetch('http://localhost/projet-web/controller/components/login/DashboardController.php?type=connexions')
        .then(res => res.json())
        .then(data => {
            const ctxConn = document.getElementById('connexionsChart').getContext('2d');
            createLineChart(ctxConn, 'Connexions', data.labels, data.values, 'rgba(75, 192, 192, 1)', 'rgba(75, 192, 192, 0.2)');
        })
        .catch(err => console.error("Erreur fetch connexions:", err));

    // --- Modifications Chart ---
    fetch('http://localhost/projet-web/controller/components/login/DashboardController.php?type=modifications')
        .then(res => res.json())
        .then(data => {
            const ctxMod = document.getElementById('modificationsChart').getContext('2d');
            createLineChart(ctxMod, 'Modifications', data.labels, data.values, 'rgba(255, 159, 64, 1)', 'rgba(255, 159, 64, 0.2)');
        })
        .catch(err => console.error("Erreur fetch modifications:", err));

});
// Fonctions pour la page de réponse
function initResponsePage() {
    // Auto-resize du textarea
    const textarea = document.querySelector('textarea');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
    
    // Confirmation avant envoi
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const response = document.getElementById('reponse').value.trim();
            if (!response) {
                e.preventDefault();
                alert('Veuillez saisir un message de réponse.');
                return false;
            }
            
            if (response.length < 10) {
                if (!confirm('Votre réponse est très courte. Êtes-vous sûr de vouloir l\'envoyer ?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    }
}

// Initialiser quand la page est chargée
document.addEventListener('DOMContentLoaded', function() {
    initResponsePage();
});


