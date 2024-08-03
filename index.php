<?php require_once("./includes/connect.php"); ?>

<?php include("./includes/header.php"); ?>
<div class="location">
    <h1>Landing Page</h1>
</div>
<?php include("./includes/LandingPage/heroSection.inc.php"); ?>
<!-- image slider -->
<?php include("./includes/LandingPage/productSlider.php"); ?>
<?php include("./includes/LandingPage/whyUs.inc.php"); ?>
<?php include("./includes/LandingPage/features.inc.php"); ?>
<?php include("./includes/LandingPage/ourTeam.inc.php"); ?>
<?php include("./includes/footer.php"); ?>
<script src="./js/index.js"></script>
<script>
    let slideIndex = 0; // Initialize slideIndex to 0 to start from the first slide
    showSlides(); // Call showSlides to start the automatic sliding

    // Function to increment/decrement slides manually
    function plusSlides(n) {
        showSlides(slideIndex += n);
    }

    // Function to jump to a specific slide
    function currentSlide(n) {
        showSlides(slideIndex = n);
    }

    // Function to show slides and handle automatic sliding
    function showSlides() {
        let i;
        let slides = document.getElementsByClassName("mySlides");
        let dots = document.getElementsByClassName("dot");

        // Hide all slides
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }

        // Increment slideIndex
        slideIndex++;

        // Reset slideIndex if it exceeds the number of slides
        if (slideIndex > slides.length) {
            slideIndex = 1;
        }

        // Remove active class from all dots
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }

        // Display the current slide and add active class to the corresponding dot
        slides[slideIndex - 1].style.display = "flex";
        dots[slideIndex - 1].className += " active";

        // Call showSlides function every 3 seconds for automatic sliding
        setTimeout(showSlides, 3000); // 3000 milliseconds = 3 seconds
    }
</script>
</body>

</html>