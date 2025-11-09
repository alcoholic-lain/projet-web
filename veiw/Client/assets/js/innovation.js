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

// === INITIALISATION ===
document.addEventListener("DOMContentLoaded", () => {
    if (location.pathname.includes("list_Innovation.html")) afficherListe();
    if (location.pathname.includes("details_Innovation.html")) afficherDetails();
    if (location.pathname.includes("a_Innovation.html")) afficherAdmin();
});
