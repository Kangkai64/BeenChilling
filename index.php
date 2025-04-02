<?php
require '_base.php';

$_title = 'BeenChilling';
include '_head.php';

$bestSeller_arr = [];

?>

    <h1 class="horizontal bestSeller">
        <span>B</span>
        <span>e</span>
        <span>s</span>
        <span>t</span>
        <span>s</span>
        <span>e</span>
        <span>l</span>
        <span>l</span>
        <span>e</span>
        <span>r</span>
    </h1>

    <div class="bestSeller">
        <div class="product-container">
            <img src="/images/product/bestSeller_mixedSundae.png" alt="bestSeller_mixedSundae.png">
            <button class="cta">Buy Now</button>
        </div>
        <div class="product-container">
            <img src="/images/product/bestSeller_bananaSplit.png" alt="bestSeller_bananaSplit.png">
            <button class="cta">Buy Now</button>
        </div>
    </div><br>

    <iframe id="video" title="vimeo-player" src="https://player.vimeo.com/video/890988764?h=05bb284c71" allowfullscreen></iframe>

    <div class="working">
        <iframe src="https://www.google.com/maps/d/u/0/embed?mid=1ekYt6jaQQaAzk3YPIMx9DHfYYcNgzls&ehbc=2E312F" width="100%" height="100%"></iframe>
    </div>

<?php
include '_foot.php';
