<?php require_once("./includes/connect.php");
include("./includes/header.php");

?>
<div class="location">
    <!-- <h1>Landing Page</h1> -->
</div>
<?php include("./includes/LandingPage/heroSection.inc.php"); ?>
</header>
<!-- image slider -->
<?php include("./includes/LandingPage/productSlider.php"); ?>
<?php include("./includes/LandingPage/whyUs.inc.php"); ?>
<?php include("./includes/LandingPage/features.inc.php"); ?>
<!-- <?php //include("./includes/LandingPage/ourTeam.inc.php"); 
        ?> -->
</main>
<?php include("./includes/footer.php"); ?>
<script src="./js/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>