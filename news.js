const apiUrl = "proxy.php";

async function displayNews() {
  try {
    const response = await fetch(apiUrl);
    const data = await response.json();

    if (data.status === "ok") {
      const articles = data.articles;
      const newsContainer = document.getElementById("news-container");

      articles.forEach((article, index) => {
        const title = article.title;
        const description = article.description;
        const source = article.source.name;
        const url = article.url;
        const imageUrl = article.urlToImage || "site_img/placeholder.jpg"; // Set default placeholder image if imageUrl is missing

        // Create the news card elements
        const newsCard = document.createElement("div");
        newsCard.classList.add("news-card");
        const newsCardBlock1 = document.createElement("div");
        newsCardBlock1.classList.add("news-card-block1");
        const newsCardBlock2 = document.createElement("div");
        newsCardBlock2.classList.add("news-card-block2");

        // Create the image element and set its source
        const newsImage = document.createElement("img");
        newsImage.classList.add("news-image");
        newsImage.src = imageUrl;

        const newsTitle = document.createElement("h1");
        newsTitle.textContent = title;

        const newsDescription = document.createElement("p");
        newsDescription.textContent = description;

        // Append news image, title, and description to the news card
        newsCard.appendChild(newsCardBlock1);
        newsCard.appendChild(newsCardBlock2);

        newsCardBlock1.appendChild(newsImage);
        newsCardBlock2.appendChild(newsTitle);
        newsCardBlock2.appendChild(newsDescription);

        // Append the news card to the news container
        newsContainer.appendChild(newsCard);

        // Add a link to the original article
        const newsLink = document.createElement("a");
        newsLink.textContent = `Read more on ${source}`;
        newsLink.href = url;
        newsLink.target = "_blank";
        newsDescription.appendChild(newsLink);

        // Add a separator between news articles
        if (index < articles.length - 1) {
          const separator = document.createElement("hr");
          newsContainer.appendChild(separator);
        }
      });
    } else {
      console.error("Error fetching news:", data.message);
    }
  } catch (error) {
    console.error("Error fetching news:", error);
  }
}

// Call the function to display news articles in the HTML format
displayNews();
