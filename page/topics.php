<?php
require '../_base.php';
$_title = 'BeenChilling';
include '../_foot.php';
include '../_head.php';
?>

<main>
        <h2 class="topics" id="slogan"><em>Have you BeenChilling?</em></h2>
        <button id="pause" onclick="playPause()"></button>
        
        <!-- Start of Navigation Bar -->
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li>
                    <div id="dropdown">
                        <a href="product.php">Product and Service</a>
                        <div id="dropdown_content">
                            <div id="dropdown_wrapper">
                                <a href="product.php#Sundaes">Sundae</a>
                                <a href="product.php#Dessert">Dessert</a>
                                <a href="product.php#Ice-Cream">Ice-Cream</a>
                            </div>
                        </div>
                    </div>
                </li>
                <li><a class="active_link" href="topics.php">Topics</a></li>
                <li><a href="reviews.php">Reviews</a></li>
                <li><a href="aboutus.php">About Us</a></li>
            </ul>
        </nav>
        <!-- End of Navigation Bar -->

        <!-- Start of Topics Navigation Bar -->
        <div id="topics_main">
            <div id="topics_nav">
                <div class="topics_nav" id="topics_old" onclick="displayEvent('old')">Old</div>
                <div class="topics_nav" id="topics_new" onclick="displayEvent('new')">New</div>
                <div class="topics_nav" id="topics_future" onclick="displayEvent('future')">Future</div>
            </div>
          <!-- End of Topics Navigation Bar -->
            
            <div class="events_container">
                <?php 
                $events = [
                    ["We&apos;re Back!", "<p>After closing for 2 years due to Covid-19, we are now officially back in business!</p><p>To celebrate, come and get the limited time only &OpenCurlyQuote;Special Mixed Sundae&CloseCurlyQuote;!</p>", "topics_images/special_mixed_sundae.png", "Special Mixed Sundae<br><br>Only at RM14.00!", "15/11/2023 ~ 30/11/2023", "old"],
                  
                    ["Noticed by John Cena!?", "<p>With our new Twitter account, we announced the re-opening of BeenChilling.</p><p>Just for fun, we tagged John Cena, not thinking anything could come from it.</p><p>But we woke up today to find that he followed our Twitter account! Could this be an opportunity of a lifetime?</p>", "", "", "16/11/2023", "old"],
                  
                    ["Welcome, John Cena!", "<p>After weeks of negotiation, John Cena is now our newest staff member!</p><p>As Strawberry Sundae is John Cena&apos;s favourite sundae, it will now be discounted for a month!</p>", "topics_images/strawberry_sundae.png", "Strawberry Sundae<br><br>Only at RM 8.00!", "1/12/2023 ~ 31/12/2023", "new"],
                  
                    ["Cat Maid VS Karen Drama!", "<p>Today, a Karen walked into the restaurant.</p><p>It was all fine and dandy until our ice cream server accidentally served her 0.001g less ice cream than the amount she wanted.</p><p>This outraged her and she started insulting our staff and asking to see the manager...</p>", "", "", "10/12/2023", "new"],
                  
                    ["20th Year Anniversary!", "<p>20 years... That&apos;s a very long time, and it&apos;s all thanks to all of our loyal customers, and that includes you!</p><p>To show our appreciation, our signature dessert, Banana Split will be discounted for a month!</p>", "topics_images/banana_split.png", "Banana Split<br><br>Only at RM 19.50!", "25/1/2024 ~ 25/2/2024", "future"]
                ];
                
                foreach ($events as $event) {
                    echo "<div class='events {$event[5]}'>";
                    echo "<div class='events_title'>{$event[0]}</div>";
                    echo "<div class='events_details'>{$event[1]}";
                    if (!empty($event[2])) {
                        echo "<div><img src='{$event[2]}' alt='{$event[3]}'><p>{$event[3]}</p></div>";
                    }
                    echo "</div>";
                    echo "<div class='events_dates'>{$event[4]}</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </main>
