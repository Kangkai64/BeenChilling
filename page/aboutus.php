<?php
require '../_base.php';

$_title = 'BeenChilling';
include '../_head.php';
?>

    <div id="aboutus_main">

        <!-- Summary -->
        <?php 
            aboutus_container(
                "aboutus_title", 
                "summary_title",
                "aboutus_contents",
                "summary_contents",
                "About Us",
                "Originated from the year 2003, we here at BeenChilling is dedicated to serve you the
                best desserts to chill you from your daily lives. Our slogan 'Have you BeenChilling?'
                reflects our goal, reminding you to take some time to relax and get a BeenChilling.
                We started out in a small store in 2003, and now 20 years later, we are still in the same small store. 
                To achieve the best experience for our customers, our BeenChillings are at a price not seen anywhere else.
                Come and chill at the only place designed to let you chill!"
            );
        ?>

        <!-- Staff -->
        <?php 
            aboutus_container(
                "aboutus_title", 
                "main_staff_title",
                "aboutus_contents",
                "main_staff_contents",
                "Our Staff",
                // John Cena
                staff_container(
                    "Public Relations Manager",
                    "john_cena.jpg",
                    "Our Public Relations Manager is none other than John Cena himself.
                    He was hired after we found out that he wanted a BeenChilling from us
                    by following our Twitter account recently. He is featured in one of our advertisement already."
                ).
                // Turkish Ice Cream Man
                staff_container(
                    "BeenChilling Server",
                    "turkish_icecream_man.jpg",
                    "The BeenChilling Server here is the notorious Turkish Ice Cream Man.
                    We&apos;ve heard that he was so good at dodging the customer&apos;s hands until
                    he dodged all of his sales, so we hired him. Do you think you can outmaneuver him?"
                ).
                // Cat Maid
                staff_container(
                    "Cashier / Security Guard",
                    "cat_maid.jpg",
                    "Our Cashier is a cute female cat maid. Don&apos;t judge a book by it&apos;s cover though,
                    with it&apos;s sharp claws, she doubles as our security guard. So if you ever plan on
                    stealing, you&apos;ll have to think twice. We pay her using canned cat foods."
                ).
                // Jeff
                staff_container(
                    "Ice Cream Maker",
                    "jeff.jpg",
                    "Our Ice Cream Maker is some guy we grabbed off the street. While he was talking on the
                    phone, we heard that he&apos;s broke and no one&apos;s hiring him. 
                    After that, we offered him the job and he accepted. He&apos;s pretty cool. His name&apos;s Jeff."
                )
            );
        ?>

        <!-- FAQ -->
        <?php 
            aboutus_container(
                "aboutus_title", 
                "main_faq_title",
                "aboutus_contents",
                "main_faq_contents",
                "FAQ",
                // FAQ 1 -->
                faq_container(
                    "Why are the ice creams called &OpenCurlyQuote;BeenChilling?&CloseCurlyQuote;",
                    "BeenChilling is a pun, which is based on the Chinese word &OpenCurlyQuote;冰淇淋&CloseCurlyQuote;.
                    The word means &OpenCurlyQuote;Ice Cream&CloseCurlyQuote; and it is pronounced as 
                    &OpenCurlyQuote;Bin Chi Lin&CloseCurlyQuote;, which is very similar to the word 
                    &OpenCurlyQuote;Been Chilling&CloseCurlyQuote;."
                ).
                
                // FAQ 2 -->
                faq_container(
                    "How was BeenChilling founded?",
                    "One day our founder, Lee Chee Kai was just chilling under a tree,
                    when a durian dropped from above and hit his head. When he woke up, he was
                    already in the hospital. Because of this tragic accident, he decided that he needed
                    to find a better place to chill. However, such a place didn't exist. This was
                    when he decided to create BeenChilling so that everyone can have a place to chill in peace."
                ).

                // FAQ 3 -->
                faq_container(
                    "Is BeenChilling open for Job Applications?",
                    "Currently, we don&apos;t accept job applications because for now, we are fully staffed.
                    Even if one of our staff resigns, we still have some people in mind to recruit because of
                    their expertise and skills. If one day we don't have anyone else to recruit, job applications
                    will be announced on our topics page."
                ).

                // FAQ 4 -->
                faq_container(
                    "What&apos;s the secret recipe for BeenChilling?",
                    "As the name implies, it is a secret, so we&apos;re not gonna reveal it. However, we can
                    say with confidence that the BeenChillings are definitely NOT made using human organs.
                    That would be silly, wouldn&apos;t it? Because it&apos;ll be too expensive!"
                ).

                // FAQ 5 -->
                faq_container(
                    "Can this website be trusted?",
                    "This website contains many security measures to make sure your personal information
                    doesn't get leaked. Ever since a hacker named &OpenCurlyQuote;Chik Soon Leong&CloseCurlyQuote; hacked
                    our website and stole our cashier&apos;s phone number (don&apos;t know why),
                    we have installed many security measures. Unfortunately, the FBI never caught the culprit
                    after he went into hiding."
                )
            );
        ?>  

        <!-- Contacts -->
        <?php 
            aboutus_container(
                "aboutus_title", 
                "main_contacts_title",
                "aboutus_contents",
                "main_contacts_contents",
                "Contacts",
                contacts_header("Oh ho, so you want to contact us? You can do so through:") .

                // Contacts 1 -->
                contacts_section(
                    "Phone Number",
                    "015-881-2629"
                ).

                // Contacts 2 -->
                contacts_section(
                    "Email",
                    "admin@beenchilling.com.my"
                ).

                // Contacts 3 -->
                contacts_section(
                    "Twitter",
                    "@beenchilling64"
                ).

                // Contacts 4 -->
                contacts_section(
                    "Located At",
                    "Dewan&nbsp;Tunku&nbsp;Abdul&nbsp;Rahman,&nbsp;TARUMT"
                ) . "</div>"
            );        
        ?>
    </div>

<?php include '../_foot.php';

