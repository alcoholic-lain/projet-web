console.log("Tunispace loaded.");

// ---------- ADMIN LOGIN ----------
const adminLoginBtn = document.getElementById("adminLoginBtn");
adminLoginBtn.addEventListener("click", () => {
    const password = prompt("Enter admin password:");
    if (password === "nadhem123") {
        alert("Admin mode enabled!");
        document.querySelectorAll(".admin-actions").forEach(el => el.style.display = "block");
    } else {
        alert("Incorrect password!");
    }
});

// ---------- LIKE & COMMENTS PER ARTICLE ----------
document.querySelectorAll(".article").forEach(article => {
  const likeBtn = article.querySelector(".like-btn");
  const likeCountEl = likeBtn.querySelector(".likeCount");
  let liked = false;

  likeBtn.addEventListener("click", () => {
    liked = !liked;
    const id = article.dataset.id;

    fetch('../controller/UpdatePost.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=like&id=${id}&liked=${liked}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        likeCountEl.textContent = data.likes;
        likeBtn.style.backgroundColor = liked ? "#00e1ff" : "transparent";
        likeBtn.style.color = liked ? "#000" : "#00e1ff";
      }
    });
  });

  const commentInput = article.querySelector(".commentInput");
  const commentBtn = article.querySelector(".commentBtn");
  const commentsList = article.querySelector(".comments-list");

  commentBtn.addEventListener("click", () => {
    const text = commentInput.value.trim();
    if (!text) return;
    const id = article.dataset.id;

    fetch('../controller/UpdatePost.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=comment&id=${id}&comment=${encodeURIComponent(text)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        const comment = document.createElement("div");
        comment.className = "comment";
        comment.textContent = text;
        commentsList.prepend(comment);
        commentInput.value = "";
      }
    });
  });

  commentInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") commentBtn.click();
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

// ---------- SEARCH FEATURE ----------
const searchInput = document.querySelector(".search-box input");
searchInput.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    const keyword = searchInput.value.toLowerCase().trim();
    const articles = document.querySelectorAll(".article");
    let found = false;

    articles.forEach(article => {
      article.classList.remove("highlight");
      if (article.dataset.keywords.toLowerCase().includes(keyword)) {
        article.classList.add("highlight");
        article.scrollIntoView({ behavior: "smooth" });
        found = true;
      }
    });

    if (!found) alert("No articles found with that keyword.");
  }
});
