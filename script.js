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

// Function to handle image click and show/hide the menu box

let menu = document.getElementById("menu");
menu.addEventListener("click", toggleMenu);

function toggleMenu() {
  let menuBox = document.getElementById("menu-box");
  if (menuBox.style.display === "block") {
    menuBox.style.display = "none";
  } else {
    menuBox.style.display = "block";
  }
}
