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
                    <img src="./adminDashOldHouse/uploads/<?php echo $image['img_path']; ?>" alt="Product Image">
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