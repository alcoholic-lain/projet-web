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
