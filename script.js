// Function to handle image click and show/hide the dialog box

let avatar = document.getElementById("avatar");
avatar.addEventListener("click", toggleDialog);

function toggleDialog() {
  let dialogBox = document.getElementById("dialog-box");
  if (dialogBox.style.display === "block") {
    dialogBox.style.display = "none";
  } else {
    dialogBox.style.display = "block";
  }
}

