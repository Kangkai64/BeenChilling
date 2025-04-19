<?php
require '../../_base.php';
$_title = 'BeenChilling';
include '../../_head.php';
?>

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
                    [
                        "title" => "We&apos;re Back!", 
                        "content" => "<p>After closing for 2 years due to Covid-19, we are now officially back in business!</p><p>To celebrate, come and get the limited time only 'Special Mixed Sundae'!</p>",
                        "image" => "/images/topic/special_mixed_sundae.png",
                        "offer" => "Special Mixed Sundae<br><br>Only at RM14.00!",
                        "date" => "15/11/2023 ~ 30/11/2023",
                        "category" => "old",
                        "style_num" => "1"
                    ],
                    [
                        "title" => "Noticed by John Cena!?",
                        "content" => "<p>With our new Twitter account, we announced the re-opening of BeenChilling.</p><p>Just for fun, we tagged John Cena, not thinking anything could come from it.</p><p>But we woke up today to find that he followed our Twitter account! Could this be an opportunity of a lifetime?</p>",
                        "image" => "",
                        "offer" => "",
                        "date" => "16/11/2023",
                        "category" => "old",
                        "style_num" => "2"
                    ],
                    [
                        "title" => "Welcome, John Cena!",
                        "content" => "<p>After weeks of negotiation, John Cena is now our newest staff member!</p><p>As Strawberry Sundae is John Cena's favourite sundae, it will now be discounted for a month!</p>",
                        "image" => "/images/topic/strawberry_sundae.png",
                        "offer" => "Strawberry Sundae<br><br>Only at RM 8.00!",
                        "date" => "1/12/2023 ~ 31/12/2023",
                        "category" => "new",
                        "style_num" => "3"
                    ],
                    [
                        "title" => "Cat Maid VS Karen Drama!",
                        "content" => "<p>Today, a Karen walked into the restaurant.</p><p>It was all fine and dandy until our ice cream server accidentally served her 0.001g less ice cream than the amount she wanted.</p><p>This outraged her and she started insulting our staff and asking to see the manager...</p>",
                        "image" => "",
                        "offer" => "",
                        "date" => "10/12/2023",
                        "category" => "new",
                        "style_num" => "4"
                    ],
                    [
                        "title" => "20th Year Anniversary!",
                        "content" => "<p>20 years... That's a very long time, and it's all thanks to all of our loyal customers, and that includes you!</p><p>To show our appreciation, our signature dessert, Banana Split will be discounted for a month!</p>",
                        "image" => "/images/topic/banana_split.png",
                        "offer" => "Banana Split<br><br>Only at RM 19.50!",
                        "date" => "25/1/2024 ~ 25/2/2024",
                        "category" => "future",
                        "style_num" => "5"
                    ]
                ];
                
                foreach ($events as $index => $event) {
                    echo "<div class='events {$event['category']}'>";
                    echo "<div id='event_title{$event['style_num']}' class='events_title'>{$event['title']}</div>";
                    echo "<div id='event_detail{$event['style_num']}' class='events_details'>{$event['content']}";
                    
                    if (!empty($event['image'])) {
                        echo "<div id='events_offer{$event['style_num']}' class='events_offer'>";
                        echo "<img src='{$event['image']}' alt='{$event['offer']}'>";
                        echo "<p>{$event['offer']}</p>";
                        echo "</div>";
                    }
                    
                    echo "</div>";
                    echo "<div id='event_date{$event['style_num']}' class='events_dates'>{$event['date']}</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

<?php
include '../../_foot.php';