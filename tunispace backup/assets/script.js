console.log("Tunispace loaded.");

// ---------- LIKE & COMMENTS PER ARTICLE ----------
document.querySelectorAll(".article").forEach(article => {
  const likeBtn = article.querySelector(".like-btn");
  const likeCountEl = article.querySelector(".likeCount");
  const commentInput = article.querySelector(".commentInput");
  const commentBtn = article.querySelector(".commentBtn");
  const commentsList = article.querySelector(".comments-list");
  const id = article.dataset.id;

  // === LIKE BUTTON ===
  likeBtn.addEventListener("click", () => {
    fetch('../controller/UpdatePost.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=like&id=${id}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        likeCountEl.textContent = data.likes;
        likeBtn.style.backgroundColor = "#00e1ff";
        likeBtn.style.color = "#000";
      }
    })
    .catch(err => console.error("Like error:", err));
  });

  // === COMMENT BUTTON ===
  commentBtn.addEventListener("click", () => {
    const text = commentInput.value.trim();
    if (!text) return;

    fetch('../controller/UpdatePost.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=comment&id=${id}&comment=${encodeURIComponent(text)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        // Add comment visually
        const div = document.createElement("div");
        div.className = "comment";
        div.innerHTML = `<strong>You:</strong> ${text} <small>just now</small>`;
        commentsList.prepend(div);
        commentInput.value = "";

        // Optional: update comment counter if you display it
        const counter = article.querySelector(".comment-counter");
        if (counter) counter.textContent = data.comment_count;
      }
    });
  });

  // Press Enter to comment
  commentInput.addEventListener("keypress", e => {
    if (e.key === "Enter") commentBtn.click();
  });
});

// ---------- SEARCH (Live search - improved) ----------
const searchInput = document.querySelector(".search-box input");
searchInput.addEventListener("input", () => {
  const query = searchInput.value.toLowerCase().trim();

  document.querySelectorAll(".article").forEach(article => {
    const title = article.querySelector(".article-title").textContent.toLowerCase();
    const content = article.querySelector(".article-text").textContent.toLowerCase();
    const keywords = (article.dataset.keywords || "").toLowerCase();

    const matches = title.includes(query) || content.includes(query) || keywords.includes(query);
    article.style.display = matches || query === "" ? "block" : "none";
  });
});
// ---------- DELETE POST ----------
document.addEventListener("click", (e) => {
    if (e.target.classList.contains("delete-btn")) {
        if (!confirm("Are you sure you want to delete this post?")) return;
        const id = e.target.dataset.id;

        fetch('../controller/DeletePost.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                e.target.closest(".article").remove();
            } else {
                alert("Failed to delete post");
            }
        });
    }
});
