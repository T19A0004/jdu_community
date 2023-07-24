//Search show
const searchForm = document.getElementById("searchForm");
const searchResult = document.querySelector(".search-result");

searchForm.addEventListener("submit", function (event) {
  event.preventDefault(); // Prevent the form from submitting normally

  // Show the search-result div
  searchResult.style.display = "flex";
});
