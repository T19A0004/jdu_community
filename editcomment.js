// JavaScript code for in-place comment editing
document.addEventListener("DOMContentLoaded", function () {
  const editButtons = document.querySelectorAll(".comment-edit-btn");
  const commentTexts = document.querySelectorAll(".comment-text");
  const editForms = document.querySelectorAll(".edit-comment-form");

  // Show edit form when clicking on the edit button
  editButtons.forEach((editButton) => {
    editButton.addEventListener("click", function () {
      const commentId = this.getAttribute("data-comment-id");
      const commentText =
        this.parentNode.parentNode.querySelector(".comment-text");
      const editForm =
        this.parentNode.parentNode.querySelector(".edit-comment-form");

      commentText.style.display = "none";
      editForm.style.display = "block";
    });
  });

  // Cancel editing when clicking on the cancel button
  editForms.forEach((editForm) => {
    const cancelButton = editForm.querySelector(".cancel-btn");
    const commentText = editForm.parentNode.querySelector(".comment-text");

    cancelButton.addEventListener("click", function () {
      editForm.style.display = "none";
      commentText.style.display = "block";
    });
  });
});
