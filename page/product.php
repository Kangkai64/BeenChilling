<?php
require '../_base.php';

$_title = 'BeenChilling';
include '../_head.php';
?>

   <?php topics_text("Get a BeenChilling like John Cena."); ?>

    <!-- product type -->
    <!--Sundaes :) -->

    <div class="container">
        <!-- Sundaes -->
        <?php 
            product_container(
                "Sundaes", 
                product("Strawberry Sundae", 8.00, "StrawberrySundae.png") .
                product("Chocolate Sundae", 8.00, "ChocolateSundae.png") .
                product("Mixed Sundae", 8.00, "MixedSundae.png") .
                product("Fruit Sundae", 8.00, "FruitSundae.png")
            ); 
        ?>
        

        <!-- Dessert -->
        <?php 
            product_container(
                "Dessert", 
                product("Banana Split", 19.50, "BananaSplit.png") .
                product("Bubble Waffle", 12.50, "BubbleWaffle.png") .
                product("Brownie A La Mode", 15.00, "brownie-ala-mode.png") .
                product("Ice-cream Sanwiches", 6.50, "Ice-creamSanwiches.png")
            ); 
        ?>

        <!-- Ice-Cream -->
        <?php 
            product_container(
                "Ice-Cream", 
                product("Banana", 4.00, "Banana.png") .
                product("Butter Pecan", 4.00, "ButterPecan.png") .
                product("Cherry", 4.00, "Cherry.png") .
                product("Chocolate", 4.00, "Chocolate.png") .
                product("Chocolate Almond", 4.00, "ChocolateAlmond.png") .
                product("Chocolate Chip", 4.00, "ChocolateChip.png") .
                product("Coconut", 4.00, "Coconut.png") .
                product("Coffee", 4.00, "Coffee.png") .
                product("Cookies 'N' Cream", 4.00, "Cookies-n-Cream.png") .
                product("Cotton Candy", 4.00, "Cotton-Candy.png") .
                product("Durian", 4.00, "Durian.png") .
                product("Green Tea", 4.00, "GreenTea.png") .
                product("Mango", 4.00, "Mango.png") .
                product("Matcha", 4.00, "Matcha.png") .
                product("Mint Chocolate Chip", 4.00, "MintChocolateChip.png") .
                product("Peach", 4.00, "Peach.png") .
                product("Raspberry Ripple", 4.00, "RaspberryRipple.png") .
                product("Strawberry", 4.00, "Strawberry.png") .
                product("Vanilla", 4.00, "Vanilla.png") .
                product("Watermelon", 4.00, "Watermelon.png")
            ); 
        ?>
    </div>

<?php
include '../_foot.php';