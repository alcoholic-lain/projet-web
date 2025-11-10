// =============================
// INNOVATION MODULE JS
// Auteur : Hichem Challakhi 🚀
// =============================

// === Données simulées ===
const innovations = [
    {
        id: 1,
        titre: "Rover LunaNova",
        categorie: "Exploration Spatiale",
        description:
            "Rover autonome conçu pour explorer les zones ombrées du pôle Sud lunaire. Équipé de panneaux solaires pliables et d’une IA capable d’analyser les roches en temps réel.",
        dateCreation: "2025-11-10",
        statut: "En attente",
        commentaires: [
            "Projet prometteur pour la mission Artemis.",
            "Très bon équilibre entre autonomie et durabilité."
        ],
        votes: { up: 132, down: 4 }
    },
    {
        id: 2,
        titre: "Satellite SolarNet",
        categorie: "Énergie Orbitale",
        description:
            "Satellite géostationnaire collectant de l’énergie solaire et la retransmettant vers la Terre par faisceaux micro-ondes. Objectif : alimenter les stations lunaires.",
        dateCreation: "2025-11-05",
        statut: "En attente",
        commentaires: [
            "Idée innovante pour la transition énergétique spatiale.",
            "Peut être combinée avec des infrastructures lunaires."
        ],
        votes: { up: 98, down: 6 }
    }
];

// === LISTE (Front Office) ===
function afficherListe() {
    const table = document.getElementById("innovation-list");
    if (!table) return;

    table.innerHTML = `
    <tr>
      <th>Titre</th>
      <th>Catégorie</th>
      <th>Date</th>
      <th>Statut</th>
    </tr>
  `;

    innovations.forEach((inv) => {
        const row = document.createElement("tr");
        row.innerHTML = `
      <td>${inv.titre}</td>
      <td>${inv.categorie}</td>
      <td>${inv.dateCreation}</td>
      <td>${inv.statut}</td>
    `;
        row.style.cursor = "pointer";
        row.addEventListener("click", () => {
            window.location.href = `details_Innovation.html?id=${inv.id}`;
        });
        table.appendChild(row);
    });
}

// === DETAILS (Front Office + Admin) ===
function afficherDetails() {
    const params = new URLSearchParams(window.location.search);
    const id = parseInt(params.get("id"));
    const inv = innovations.find((i) => i.id === id);
    if (!inv) return;

    document.getElementById("titre").textContent = inv.titre;
    document.getElementById("desc").textContent = inv.description;
    document.getElementById("date").textContent = inv.dateCreation;
    document.getElementById("statut").textContent = inv.statut;
    document.getElementById("upvotes").textContent = inv.votes.up;
    document.getElementById("downvotes").textContent = inv.votes.down;

    const ul = document.getElementById("commentaires");
    inv.commentaires.forEach((c) => {
        const li = document.createElement("li");
        li.textContent = c;
        ul.appendChild(li);
    });

    // Boutons admin s’ils existent dans la page détails
    const btnValider = document.getElementById("btn-valider");
    const btnRejeter = document.getElementById("btn-rejeter");

    if (btnValider && btnRejeter) {
        btnValider.addEventListener("click", () => {
            inv.statut = "Validée ✅";
            document.getElementById("statut").textContent = inv.statut;
        });

        btnRejeter.addEventListener("click", () => {
            inv.statut = "Rejetée ❌";
            document.getElementById("statut").textContent = inv.statut;
        });
    }
}

// === ADMIN (Back Office) ===
function afficherAdmin() {
    const table = document.getElementById("admin-table");
    if (!table) return;

    table.innerHTML = "";
    innovations.forEach((inv) => {
        const row = document.createElement("tr");
        row.innerHTML = `
      <td style="cursor:pointer; color:#8A8DFF;" onclick="ouvrirDetails(${inv.id})">${inv.titre}</td>
      <td>${inv.categorie}</td>
      <td>${inv.dateCreation}</td>
      <td id="statut-${inv.id}">${inv.statut}</td>
      <td>
        <button class="valider" onclick="validerInnovation(${inv.id})">Valider</button>
        <button class="rejeter" onclick="rejeterInnovation(${inv.id})">Rejeter</button>
      </td>
    `;
        table.appendChild(row);
    });
}

function ouvrirDetails(id) {
    window.location.href = `../../Client/src/details_Innovation.html?id=${id}`;
}

function validerInnovation(id) {
    const inv = innovations.find((i) => i.id === id);
    if (inv) {
        inv.statut = "Validée ✅";
        document.getElementById(`statut-${id}`).textContent = inv.statut;
    }
}

function rejeterInnovation(id) {
    const inv = innovations.find((i) => i.id === id);
    if (inv) {
        inv.statut = "Rejetée ❌";
        document.getElementById(`statut-${id}`).textContent = inv.statut;
    }
}
function ajouterInnovation() {
    const form = document.getElementById("form-innovation");
    if (!form) return;

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const titre = document.getElementById("titre").value.trim();
        const categorie = document.getElementById("categorie").value.trim();
        const description = document.getElementById("description").value.trim();
        const pieceJointe = document.getElementById("pieceJointe").files[0];
        const msg = document.getElementById("msg");

        if (!titre || !categorie || !description) {
            msg.textContent = "⚠️ Tous les champs sont obligatoires.";
            msg.className = "error";
            return;
        }

        const newInnovation = {
            id: innovations.length + 1,
            titre,
            categorie,
            description,
            dateCreation: new Date().toISOString().split("T")[0],
            statut: "En attente",
            commentaires: [],
            votes: { up: 0, down: 0 },
            piece: pieceJointe ? pieceJointe.name : null,
        };

        innovations.push(newInnovation);

        msg.textContent = "✅ Innovation soumise avec succès !";
        msg.className = "success";

        // Réinitialiser le formulaire
        form.reset();

        // Rediriger vers la liste après 2 secondes
        setTimeout(() => {
            window.location.href = "list_Innovation.html";
        }, 2000);
    });
}
// ---------- ADD PAGE: preview + submit (no blocking) ----------
function setupAddInnovationPage() {
    const form = document.getElementById("form-innovation");
    if (!form) return;

    const titreEl = document.getElementById("titre");
    const catEl = document.getElementById("categorie");
    const descEl = document.getElementById("description");
    const fileEl = document.getElementById("pieceJointe");
    const msg = document.getElementById("msg");
    const submitBtn = form.querySelector(".btn-submit");

    // ---- Preview image (optional) ----
    const preview = document.createElement("img");
    preview.id = "apercu-image";
    preview.style.display = "none";
    preview.style.maxWidth = "100%";
    preview.style.marginTop = "15px";
    preview.style.borderRadius = "10px";
    preview.style.boxShadow = "0 0 15px rgba(74,77,231,0.5)";
    fileEl.parentNode.insertBefore(preview, fileEl.nextSibling);

    fileEl.addEventListener("change", (e) => {
        const f = e.target.files[0];
        if (f && f.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = ev => {
                preview.src = ev.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(f);
        } else {
            preview.style.display = "none";
            preview.src = "";
        }
    });

    // ---- Submit handler (uses native validation) ----
    form.addEventListener("submit", (e) => {
        e.preventDefault();

        // Trigger native ‘required’ checks; if invalid, stop here.
        if (!form.reportValidity()) return;

        // Small rocket animation – not blocking
        submitBtn.classList.add("decollage");
        setTimeout(() => submitBtn.classList.remove("decollage"), 1500);

        // Build the new innovation (file is optional)
        const newInnovation = {
            id: innovations.length + 1,
            titre: (titreEl.value || "").trim(),
            categorie: (catEl.value || "").trim(),
            description: (descEl.value || "").trim(),
            dateCreation: new Date().toISOString().split("T")[0],
            statut: "En attente",
            commentaires: [],
            votes: { up: 0, down: 0 },
            piece: fileEl.files[0] ? fileEl.files[0].name : null
        };

        // Basic JS-level check in case attributes ‘required’ were removed
        if (!newInnovation.titre || !newInnovation.categorie || !newInnovation.description) {
            msg.textContent = "⚠️ Tous les champs (sauf la pièce jointe) sont obligatoires.";
            msg.className = "error";
            return;
        }

        innovations.push(newInnovation);

        msg.textContent = "✅ Innovation soumise avec succès !";
        msg.className = "success";
        form.reset();
        preview.style.display = "none";

        // Redirect to list
        setTimeout(() => { window.location.href = "list_Innovation.html"; }, 1800);
    });
}


// === INITIALISATION ===
document.addEventListener("DOMContentLoaded", () => {
    const path = location.pathname;

    if (path.includes("add_Innovation.html")) setupAddInnovationPage();
    if (location.pathname.includes("add_Innovation.html")) ajouterInnovation();
    if (location.pathname.includes("list_Innovation.html")) afficherListe();
    if (location.pathname.includes("details_Innovation.html")) afficherDetails();
    if (location.pathname.includes("a_Innovation.html")) afficherAdmin();
});
