document.addEventListener("DOMContentLoaded", () => {
    const card = document.querySelector(".post-card");

    if (card) {
        card.style.opacity = "0";
        card.style.transform = "translateY(8px)";
        setTimeout(() => {
            card.style.transition = ".35s ease";
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }, 80);
    }
});
document.addEventListener("DOMContentLoaded", () => {

    const buttons = document.querySelectorAll(".vote-btn");
    const scoreEl = document.getElementById("voteScore");

    // ✅ Récupère l'id depuis l'URL → 100 % fiable
    const params = new URLSearchParams(window.location.search);
    const innovationId = params.get("id");

    if (!innovationId) {
        console.error("ID innovation introuvable dans l'URL");
        return;
    }

    buttons.forEach(btn => {
        btn.addEventListener("click", () => {

            const voteType = btn.dataset.vote;

            fetch("../../../../controller/components/Innovation/VoteAction.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    innovation_id: innovationId,
                    vote_type: voteType
                })
            })
                .then(res => res.json())
                .then(data => {

                    if (data.error === "not_logged") {
                        alert("Vous devez être connecté pour voter.");
                        return;
                    }

                    if (!data.success) return;

                    // ✅ Mise à jour du score
                    scoreEl.innerText = data.score;

                    // reset visuel
                    buttons.forEach(b => b.classList.remove("active"));

                    // vote actif
                    if (data.userVote === "up") {
                        document.querySelector(".upvote")?.classList.add("active");
                    }
                    else if (data.userVote === "down") {
                        document.querySelector(".downvote")?.classList.add("active");
                    }
                })
                .catch(err => console.error("Erreur vote:", err));

        });
    });
});
function openModal(fileUrl, ext) {
    const modal = document.getElementById("fileModal");
    const content = document.getElementById("modalContent");
    const downloadBtn = document.getElementById("downloadBtn");

    content.innerHTML = "";

    if (["jpg","jpeg","png","gif","webp"].includes(ext)) {
        content.innerHTML = `<img src="${fileUrl}">`;
    }
    else if (ext === "pdf") {
        content.innerHTML = `<iframe src="${fileUrl}" width="100%" height="600px"></iframe>`;
    }
    else {
        content.innerHTML = `<p style="color:white">Aperçu non disponible</p>`;
    }

    downloadBtn.href = fileUrl;
    modal.style.display = "flex";
}

function closeModal() {
    document.getElementById("fileModal").style.display = "none";
}