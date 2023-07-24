// Get the file input element
const fileInput = document.getElementById("upload");

// Get the preview container element
const previewContainer = document.getElementById("preview-container");

// Listen for changes in the file input
fileInput.addEventListener("change", function (event) {
  previewContainer.innerHTML = ""; // Clear the preview container

  const file = event.target.files[0]; // Get the selected file

  if (file) {
    const reader = new FileReader();

    // Set up the FileReader onload event
    reader.onload = function () {
      const img = document.createElement("img");
      img.src = reader.result;
      img.classList.add("preview-image");
      previewContainer.appendChild(img);
    };

    // Read the selected file as a Data URL
    reader.readAsDataURL(file);
  }
});
