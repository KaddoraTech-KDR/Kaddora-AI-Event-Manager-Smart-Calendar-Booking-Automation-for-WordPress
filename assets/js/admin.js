document.addEventListener("click", function (e) {
  if (e.target.classList.contains("kaem-delete-booking")) {
    if (!confirm("Delete this booking?")) return;

    let index = e.target.dataset.index;
    let postId = e.target.dataset.post;

    const url =
      typeof ajaxurl !== "undefined" ? ajaxurl : "/wp-admin/admin-ajax.php";

    fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=kaem_delete_booking&index=${index}&post_id=${postId}`,
    })
      .then((res) => res.text())
      .then((data) => {
        if (data.includes("Deleted")) {
          location.reload();
        } else {
          alert("Delete failed");
        }
      })
      .catch(() => {
        alert("Error");
      });
  }

  if (e.target.id === "kaem-generate-desc") {
    sendAIRequest("desc");
  }

  if (e.target.id === "kaem-generate-seo") {
    sendAIRequest("seo");
  }

  if (e.target.id === "kaem-generate-tags") {
    sendAIRequest("tags");
  }
});

function sendAIRequest(type) {
  let title = "";

  if (typeof wp !== "undefined" && wp.data) {
    title = wp.data.select("core/editor").getEditedPostAttribute("title");
  } else {
    let el = document.getElementById("title");
    title = el ? el.value : "";
  }

  if (!title) {
    alert("Please enter event title first");
    return;
  }

  const url =
    typeof ajaxurl !== "undefined" ? ajaxurl : "/wp-admin/admin-ajax.php";

  fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `action=kaem_ai_generate&type=${type}&title=${encodeURIComponent(title)}`,
  })
    .then((res) => res.text())
    .then((result) => {
      let resultBox = document.getElementById("kaem-ai-result");
      if (resultBox) resultBox.innerHTML = result;

      if (type === "desc") {
        if (typeof wp !== "undefined" && wp.data) {
          wp.data.dispatch("core/editor").editPost({ content: result });
        } else {
          let content = document.getElementById("content");
          if (content) content.value = result;
        }
      }
    })
    .catch(() => {
      alert("Error");
    });
}
