/* =========================================================
   🌌 STARFIELD BACKGROUND
========================================================= */

const canvas = document.getElementById('galaxyCanvas');
const ctx = canvas.getContext('2d');
canvas.width = innerWidth;
canvas.height = innerHeight;

const stars = Array.from({ length: 450 }, () => ({
    x: Math.random() * canvas.width,
    y: Math.random() * canvas.height,
    r: Math.random() * 1.7 + 0.4,
    a: Math.random(),
    s: Math.random() * 0.6 + 0.2
}));

function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    stars.forEach(s => {
        ctx.beginPath();
        ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(255,255,255, ${
            document.documentElement.classList.contains("dark") ? s.a : s.a * 0.3
        })`;
        ctx.fill();

        s.a += (Math.random() - 0.5) * 0.04;
        s.a = Math.max(0.3, Math.min(1, s.a));
        s.x += s.s;
        if (s.x > canvas.width) s.x = 0;
    });
    requestAnimationFrame(animate);
}
animate();

window.addEventListener("resize", () => {
    canvas.width = innerWidth;
    canvas.height = innerHeight;
});

/* =========================================================
   🌗 THEME SWITCHER
========================================================= */

const themeSwitch = document.getElementById("theme-switch");

if (localStorage.getItem("theme") === "light") {
    document.documentElement.classList.remove("dark");
}

themeSwitch.checked = document.documentElement.classList.contains("dark");

themeSwitch.addEventListener("change", () => {
    document.documentElement.classList.toggle("dark");
    localStorage.setItem(
        "theme",
        document.documentElement.classList.contains("dark") ? "dark" : "light"
    );
});

/* =========================================================
   👤 USER DROPDOWN
========================================================= */

document.getElementById("user-avatar").addEventListener("click", e => {
    e.stopPropagation();
    document.getElementById("user-dropdown").classList.toggle("show");
});
document.addEventListener("click", () =>
    document.getElementById("user-dropdown").classList.remove("show")
);

/* =========================================================
   🧭 PREVENT INDEX.HTML AJAX NAVIGATION
========================================================= */

const contentArea = document.getElementById("content-area");
if (!contentArea) {
    console.log("Navigation AJAX désactivée sur cette page.");
}

/* =========================================================
   💬 POPUP MESSENGER + PERSISTENCE
========================================================= */

const dmPopup = document.getElementById("dm-popup");
const conversationsPanel = document.getElementById("conversations-panel");
const chatPanel = document.getElementById("chat-panel");
const messagesDiv = document.getElementById("popup-messages");
const backBtn = document.getElementById("back-to-list");
const popupAvatar = document.getElementById("popup-avatar");
const popupName = document.getElementById("popup-name");
const popupStatus = document.getElementById("popup-status");
const input = document.getElementById("popup-input");
const dmBtn = document.getElementById("dm-btn");
const minimizeBtn = document.getElementById("minimize-btn");
const maximizeBtn = document.getElementById("maximize-btn");
const closeBtn = document.getElementById("close-popup");

let currentChat = null;

/* =========================================================
   💾 PERSISTENCE FUNCTIONS
========================================================= */

function saveDMState() {
    const state = {
        open: dmPopup.style.display === "flex",
        minimized: dmPopup.classList.contains("minimized"),
        maximized: dmPopup.classList.contains("maximized"),
        currentChat: currentChat
    };
    localStorage.setItem("dm_state", JSON.stringify(state));
}

function loadDMState() {
    const saved = localStorage.getItem("dm_state");
    if (!saved) return;

    const state = JSON.parse(saved);

    if (state.open) dmPopup.style.display = "flex";
    if (state.minimized) dmPopup.classList.add("minimized");
    if (state.maximized) dmPopup.classList.add("maximized");
    if (state.currentChat) openChat(state.currentChat);
}

/* =========================================================
   🔄 LOAD PREVIOUS STATE AT STARTUP
========================================================= */
loadDMState();

/* =========================================================
   🚀 POPUP OPEN & CLOSE
========================================================= */

dmBtn.addEventListener("click", () => {
    dmPopup.style.display = "flex";
    saveDMState();
});

closeBtn.addEventListener("click", () => {
    dmPopup.style.display = "none";
    showList();
    saveDMState();
});

minimizeBtn.addEventListener("click", () => {
    dmPopup.classList.toggle("minimized");
    saveDMState();
});

maximizeBtn.addEventListener("click", () => {
    dmPopup.classList.toggle("maximized");
    saveDMState();
});

/* =========================================================
   📩 CHAT MANAGEMENT
========================================================= */

function showList() {
    currentChat = null;
    backBtn.style.display = "none";
    popupAvatar.style.opacity = "0";
    popupAvatar.src = "";
    popupName.textContent = "Messages";
    popupStatus.textContent = "";
    conversationsPanel.classList.remove("hidden");
    chatPanel.classList.add("hidden");
    saveDMState();
}
backBtn.addEventListener("click", showList);

function openChat(key) {
    currentChat = key;
    const c = conversations[key];

    backBtn.style.display = "block";
    popupAvatar.style.opacity = "1";
    popupAvatar.src = c.avatar;

    popupName.textContent = c.name;
    popupStatus.textContent = c.online ? "En ligne" : "Hors ligne";

    conversationsPanel.classList.add("hidden");
    chatPanel.classList.remove("hidden");

    messagesDiv.innerHTML = "";
    c.messages.forEach(m => {
        const div = document.createElement("div");
        div.className = `message-item ${m.sent ? "sent" : ""}`;
        const bubble = document.createElement("div");
        bubble.className = "bubble";
        bubble.textContent = m.text;
        div.appendChild(bubble);
        messagesDiv.appendChild(div);
    });
    messagesDiv.scrollTop = messagesDiv.scrollHeight;

    saveDMState();
}

document.querySelectorAll(".contact-item").forEach(el =>
    el.addEventListener("click", () => openChat(el.dataset.contact))
);

document.getElementById("popup-send").addEventListener("click", () => {
    if (input.value.trim() && currentChat) {
        conversations[currentChat].messages.push({
            text: input.value,
            sent: true
        });

        const div = document.createElement("div");
        div.className = "message-item sent";

        const bubble = document.createElement("div");
        bubble.className = "bubble";
        bubble.textContent = input.value;

        div.appendChild(bubble);
        messagesDiv.appendChild(div);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;

        input.value = "";
        saveDMState();
    }
});

input.addEventListener("keypress", e => {
    if (e.key === "Enter") document.getElementById("popup-send").click();
});

/* =========================================================
   🗂️ CONVERSATION DATA
========================================================= */

const conversations = {
    sarah: {
        name: "Sarah Johnson",
        avatar: "https://randomuser.me/api/portraits/women/32.jpg",
        online: true,
        messages: [
            { text: "Hey! Comment ça va ?", sent: false },
            { text: "Super et toi !", sent: true }
        ]
    },
    mike: {
        name: "Mike Chen",
        avatar: "https://randomuser.me/api/portraits/men/45.jpg",
        online: false,
        messages: [
            { text: "Réunion à 15h", sent: false }
        ]
    },
    emma: {
        name: "Emma Davis",
        avatar: "https://randomuser.me/api/portraits/women/68.jpg",
        online: true,
        messages: [
            { text: "Merci !", sent: false }
        ]
    }
};
