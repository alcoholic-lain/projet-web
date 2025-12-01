function confirmInnovation(id, statut) {

    if (!confirm("Confirmer le changement de statut : " + statut + " ?")) {
        return;
    }

    fetch('/projet-web/controller/api/admin/confirm_innovation.php', {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `innovation_id=${encodeURIComponent(id)}&statut=${encodeURIComponent(statut)}`
    })
        .then(response => response.text())
        .then(text => {
            console.log("Réponse brute API :", text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                alert("❌ Réponse invalide de l'API");
                return;
            }

            if (data.success) {
                alert("✅ Statut mis à jour + email envoyé !");
                location.reload();
            } else {
                alert("❌ Erreur API : " + (data.error || "inconnue"));
            }
        })
        .catch(err => {
            console.error("Erreur réseau :", err);
            alert("❌ Erreur réseau");
        });
}
