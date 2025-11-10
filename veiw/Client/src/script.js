let likeCount = 0;
let liked = false;

const likeBtn = document.getElementById("likeBtn");
const likeCountEl = document.getElementById("likeCount");
const commentBtn = document.getElementById("commentBtn");
const commentInput = document.getElementById("commentInput");
const commentsList = document.getElementById("commentsList");

likeBtn.addEventListener("click", () => {
    liked = !liked;
    likeCount += liked ? 1 : -1;
    likeCountEl.textContent = likeCount;
    likeBtn.style.background = liked ? "#00e1ff" : "none";
    likeBtn.style.color = liked ? "#000" : "#00e1ff";
});

commentBtn.addEventListener("click", () => {
    const text = commentInput.value.trim();
    if (text === "") return;

    const comment = document.createElement("div");
    comment.classList.add("comment");
    comment.textContent = text;

    commentsList.prepend(comment);
    commentInput.value = "";
});
