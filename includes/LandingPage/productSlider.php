<?php
include "./includes/Shop/readProducts.php";

$productsClass = new Products();
$images = $productsClass->getProductImagesWithNamesLimited(12); // Fetch only 12 images with names

// Divide images into slides
$imagesPerSlide = 3; // Number of images per slide
$totalImages = count($images);
$totalSlides = ceil($totalImages / $imagesPerSlide);
?>

<div class="slideshow-container">
    <?php for ($slideIndex = 0; $slideIndex < $totalSlides; $slideIndex++) : ?>
        <div class="mySlides fade">
            <?php for ($imageIndex = 0; $imageIndex < $imagesPerSlide; $imageIndex++) : ?>
                <?php
                $imageNumberInArray = ($slideIndex * $imagesPerSlide) + $imageIndex;
                if ($imageNumberInArray >= $totalImages) break;
                $image = $images[$imageNumberInArray];
                ?>
                <div class="product">
                    <img src=".<?php echo htmlspecialchars($image['img_path'], ENT_QUOTES, 'UTF-8'); ?>" style="width:100px">
                    <div class="text"><?php echo htmlspecialchars($image['pro_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            <?php endfor; ?>
        </div>
    <?php endfor; ?>

    <a class="prev" onclick="plusSlides(-1)">❮</a>
    <a class="next" onclick="plusSlides(1)">❯</a>
</div>
<br>

<div style="text-align:center">
    <?php for ($i = 1; $i <= $totalSlides; $i++) : ?>
        <span class="dot" onclick="currentSlide(<?php echo $i; ?>)"></span>
    <?php endfor; ?>
</div>