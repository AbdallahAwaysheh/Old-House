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
    let slideIndex = 1;
    showSlides(slideIndex);

    function plusSlides(n) {
        showSlides(slideIndex += n);
    }

    function currentSlide(n) {
        showSlides(slideIndex = n);
    }

    function showSlides(n) {
        let i;
        let slides = document.getElementsByClassName("mySlides");
        let dots = document.getElementsByClassName("dot");
        if (n > slides.length) {
            slideIndex = 1
        }
        if (n < 1) {
            slideIndex = slides.length
        }
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }
        slides[slideIndex - 1].style.display = "flex";
        dots[slideIndex - 1].className += " active";
    }
</script>
</body>

</html>