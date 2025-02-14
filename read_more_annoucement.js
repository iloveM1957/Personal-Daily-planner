document.querySelectorAll(".read-more-btn").forEach(button => {
    button.addEventListener("click", function () {
        let announcement = this.previousElementSibling; // Get the announcement content
        let shortText = announcement.querySelector(".short-text");
        let fullText = announcement.querySelector(".full-text");

        if (fullText.style.display === "none" || fullText.style.display === "") {
            fullText.style.display = "inline"; // Show full text
            shortText.style.display = "none"; // Hide short text
            this.textContent = "Read Less"; // Change button text
        } else {
            fullText.style.display = "none"; // Hide full text
            shortText.style.display = "inline"; // Show short text
            this.textContent = "Read More"; // Change button text
        }
    });
});
