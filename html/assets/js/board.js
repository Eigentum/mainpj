// apply Asynchronous Process to reply_form
document.querySelector("#comment-form").addEventListener("submit", function(e) {
    e.preventDefault();

    let comment = document.querySelector("textarea[name='comment']").value;
    let postId = document.querySelector("input[name='post_id']").value;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "comment.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            document.querySelector("#comment-list").innerHTML = xhr.responseText;
            document.querySelector("textarea[name='comment']").value = '';  // Reset Reply_field
        }
    };

    xhr.send("comment=" + encodeURIComponent(comment) + "&post_id=" + postId);
});

// Apply Asynchronous process to modifying Post
document.querySelector("#edit-form").addEventListener("submit", function(e) {
    e.preventDefault();

    let content = document.querySelector("textarea[name='content']").value;
    let postId = document.querySelector("input[name='post_id']").value;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "edit_post.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            document.querySelector("#post-content").innerHTML = xhr.responseText;
            document.querySelector("#edit-form").style.display = 'none';  // 수정 폼 숨기기
        }
    };

    xhr.send("content=" + encodeURIComponent(content) + "&post_id=" + postId);
});

