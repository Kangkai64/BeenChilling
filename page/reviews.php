<?php
require '../_base.php';
$_title = 'BeenChilling';
include '../_head.php';
?>

<body>
   <main> 
            <h1 class="horizontal">
                <span>R</span>
                <span>e</span>
                <span>v</span>
                <span>i</span>
                <span>e</span>
                <span>w</span>
                <span>s</span>
            </h1>

        <!-- Reviews -->
        <?php
        $reviews = [
            [
                "image" => "/images/reviews/review_1.jpg",
                "name" => "LikeGuy👍64",
                "text" => "I'm a cheeky guy who likes to try out new things. 
                           BeenChilling happens to be nearby and here I come. 
                           I ordered their best seller, and it actually tasted good yet affordable. 
                           It is a place worth staying in this hot summer and I would like to visit here again."
            ],
            [
                "image" => "/images/reviews/review_2.gif",
                "name" => "HeyMan😝Happy",
                "text" => "Hello, readers. I found BeenChilling on the Internet. 
                           I saw their promotion so I paid them a visit. 
                           It's real. I have never chilled like this before. 
                           My profile picture was literally my reaction 
                           when I took my first bite of my banana split. 
                           I'm surprised, and I will definitely recommend it 
                           to all my friends. <br><br>
                           P.S.: The security cat is cute though."
            ],
            [
                "image" => "/images/reviews/review_3.jpg",
                "name" => "Sukuna👑KingOfCurse",
                "text" => "Stand Proud. You have BeenChilling."
            ],
            [
                "image" => "/images/reviews/review_4.jpg",
                "name" => "2.5jo Satoru",
                "text" => "BeenChilling is insanely foreign delicious, 
                           and they haven't given it all they had. 
                           Honestly, I don't think I wouldn't come even if they didn't have John Cena. 
                           Still, I kinda feel sorry for them. I didn't make it for their opening ceremony. 
                           I had fun. I am glad I got diabetes because of having BeenChilling. 
                           It'd have been embarrassing if I let some strong opponent or old age get the best of me. <br><br>
                           P.S. : Those at my mouth are chocolate sundae. I accidentally spilled it on my mouth."
            ],
            [
                "image" => "/images/reviews/review_5.jpg",
                "name" => "OppenSmileLOL",
                "text" => "Now I am become Death. The Destroyer of World. The Turkish Ice Cream Man give me a hard time.
                 I just want an ice-cream, but since I can't outmaneuver him, I ended up having a banana split.
                 It made me feels exhausted and happy at the same time. What a day!"
            ],
            [
                "image" => "/images/reviews/review_6.jpeg",
                "name" => "PsychoPhysicist",
                "text" => "BeenChilling from yesterday,<br> BeenChilling for today,<br> BeenChilling for tomorrow.<br> The important thing is not to stop BeenChilling.<br><br> - Not by Albert Einstein"
            ],
            [
                "image" => "/images/reviews/review_7.jpg",
                "name" => "JungUn Oppa",
                "text" => "Anyeonghasaeyo! I'm your JungUn Oppa from North Korea. I will recommend BeenChilling to you guys, and you must come here in a month or I'll give you a free \"nuke\" and a \"vaccine\"."
            ],
            [
                "image" => "/images/reviews/review_8.jpg",
                "name" => "Christopher Columbus",
                "text" => "I came looking for copper and I found BeenChilling."
            ],
            [
                "image" => "/images/reviews/review_9.jpg",
                "name" => "Mr.Philosopher",
                "text" => "I BeenChilling, therefore, I am."
            ]
        ];
        ?>

        <div id="reviews">
            <?php foreach ($reviews as $review) : ?>
                <div class="reviews">
                    <?php if ($review['name'] === "2.5jo Satoru") : ?>
                        <img src="<?= $review['image'] ?>" alt="profile pic" 
                        onmouseover="this.src='/images/reviews/review_4.jpg'" 
                        onmouseout="this.src='/images/reviews/review_4_like.jpg'">
                    <?php else : ?>
                        <img src="<?= $review['image'] ?>" alt="profile pic">
                    <?php endif; ?>
                    <span><?= $review['name'] ?></span>
                    <p><?= $review['text'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Top button -->
        <button id="top" class="fas fa-arrow-up" onclick="topFunction()"></button>
        <br><br>
    </main>
    
</body>
</html>
<?php
include '../_foot.php';