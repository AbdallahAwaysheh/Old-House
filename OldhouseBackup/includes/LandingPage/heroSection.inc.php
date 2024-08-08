<div class="hero-section fade">
    <section class="sectiondisplay" id="home">

        <div class="massege">
            <div>
                <h1>Buy and sell used household iteams <br> easily and secuerly online</h1>
                <p class="letter">
                    we are happy to give you a great offers
                </p>
            </div>
            <div class="hero-buttons">
                <a href="<?php
                            if (isset($userID)) {
                                echo "./shop.php";
                            } else {
                                echo "./adminDashOldHouse/login/login.php";
                            }
                            ?>">start now</a>
                <a href="#" class="calltoaction">explore</a>
            </div>
        </div>
        <div class="img">
            <img src="./images/couch.png" alt="505">
        </div>

    </section>
</div>