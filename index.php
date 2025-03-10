<?php
require '_base.php';

$_title = 'BeenChilling';
include '_head.php';
?>

<?php topics_text("Get a BeenChilling like John Cena."); ?>
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
            <?php menu("Banana Split", 19.50, ["Fresh Banana", "Belgium Chocalate", "French Vanilla"], "BananaSplit.png"); ?>
            <?php menu("Mixed Sundae", 8.00, ["Classic Vanilla", "Belgium Chocalate", "Fresh Strawberries"], "MixedSundae.png"); ?>
        </div><br>

        <iframe id="video" title="vimeo-player" src="https://player.vimeo.com/video/890988764?h=05bb284c71" allowfullscreen></iframe>

        <div class="working">
        <iframe style="float: left;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.535559963399!2d101.7304667749715!3d3.215831796759347!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc39eb601d80e9%3A0x9f463605d7af4001!2sTAR%20UMT%20East%20Campus%20Gate!5e0!3m2!1sen!2smy!4v1740114483145!5m2!1sen!2smy" width="400" height="380" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            <p>From 12:30 P.M. until 10:00 P.M. <br> From Thursday to Tuesday <br> (Close for Wednesday) </p>
        </div>

<?php
include '_foot.php';