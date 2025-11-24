// view/F/assets/js/chat.js

document.addEventListener("DOMContentLoaded", function () {
    // Auto-scroll messages to bottom
    var msgBox = document.getElementById("messages");
    if (msgBox) {
        msgBox.scrollTop = msgBox.scrollHeight;
    }

    // Toggle inline edit form
    document.querySelectorAll(".message .edit-message").forEach(function (btn) {
        btn.addEventListener("click", function () {
            var messageEl = this.closest(".message");
            if (!messageEl) return;
            var form = messageEl.querySelector(".edit-message-form");
            var textDiv = messageEl.querySelector(".text");
            if (form && textDiv) {
                form.classList.remove("d-none");
                textDiv.classList.add("d-none");
            }
        });
    });

    document.querySelectorAll(".message .cancel-edit").forEach(function (btn) {
        btn.addEventListener("click", function () {
            var messageEl = this.closest(".message");
            if (!messageEl) return;
            var form = messageEl.querySelector(".edit-message-form");
            var textDiv = messageEl.querySelector(".text");
            if (form && textDiv) {
                form.classList.add("d-none");
                textDiv.classList.remove("d-none");
            }
        });
    });

    // ===== DARK MODE TOGGLE =====
    var body = document.body;
    var toggle = document.getElementById("themeToggle");

    // Load saved theme
    var savedTheme = localStorage.getItem("chatTheme");
    if (savedTheme === "dark") {
        body.classList.remove("theme-light");
        body.classList.add("theme-dark");
        if (toggle) toggle.textContent = "☀️";
    } else {
        body.classList.remove("theme-dark");
        body.classList.add("theme-light");
        if (toggle) toggle.textContent = "🌙";
    }

    if (toggle) {
        toggle.addEventListener("click", function () {
            var isDark = body.classList.contains("theme-dark");
            if (isDark) {
                body.classList.remove("theme-dark");
                body.classList.add("theme-light");
                localStorage.setItem("chatTheme", "light");
                toggle.textContent = "🌙";
            } else {
                body.classList.remove("theme-light");
                body.classList.add("theme-dark");
                localStorage.setItem("chatTheme", "dark");
                toggle.textContent = "☀️";
            }
        });
    }
});
