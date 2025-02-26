<?php
require '../_base.php';

$_title = 'BeenChilling';
include '../_head.php';
?>
<body>
    <audio id="home" loop>
        <source src="home.mp3" type="audio/mp3">
        Your browser does not support the audio element.
    </audio>
    <br>

    <main>

        <div id="aboutus_main">

            <!-- Summary -->
            <div class="aboutus_container">
                <div class="aboutus_title" id="summary_title">About Us</div>
                <div class="aboutus_contents" id="summary_contents">
                    Originated from the year 2003, we here at BeenChilling is dedicated to serve you the
                    best desserts to chill you from your daily lives. Our slogan &OpenCurlyQuote;Have you BeenChilling?&CloseCurlyQuote;
                    reflects our goal, reminding you to take some time to relax and get a BeenChilling.
                    We started out in a small store in 2003, and now 20 years later, we are still in the same small store. 
                    To achieve the best experience for our customers, our BeenChillings are at a price not seen anywhere else.
                    Come and chill at the only place designed to let you chill!
                </div>
            </div>

            <!-- Staff -->
            <div class="aboutus_container">
                <div class="aboutus_title" id="main_staff_title">Our Staff</div>
                <div class="aboutus_contents" id="main_staff_contents">

                    <!-- John Cena -->
                    <div class="staff_role">Public&nbsp;Relations&nbsp;Manager</div>
                    <div class="staff_container">
                        <img class="aboutus-images" src="/images/aboutus/john_cena.jpg" alt="John Cena">
                        <p>
                            Our Public Relations Manager is none other than John Cena himself.
                            He was hired after we found out that he wanted a BeenChilling from us
                            by following our Twitter account recently. He is featured in one of our advertisement already.
                        </p>
                    </div>
                    <!-- Turkish Ice Cream Man -->
                    <div class="staff_role">BeenChilling&nbsp;Server</div>
                    <div class="staff_container">
                        <img class="aboutus-images" src="/images/aboutus/turkish_icecream_man.jpg" alt="Turkish Ice Cream Man">
                        <p>
                            The BeenChilling Server here is the notorious Turkish Ice Cream Man.
                            We&apos;ve heard that he was so good at dodging the customer&apos;s hands until
                            he dodged all of his sales, so we hired him. Do you think you can outmaneuver him?
                        </p>
                    </div>
                    <!-- Cat Maid -->
                    <div class="staff_role">Cashier&nbsp;/&nbsp;Security&nbsp;Guard</div>
                    <div class="staff_container">
                        <img class="aboutus-images" src="/images/aboutus/cat_maid.jpg" alt="Cat Maid">
                        <p>
                            Our Cashier is a cute female cat maid. Don&apos;t judge a book by it&apos;s cover though,
                            with it&apos;s sharp claws, she doubles as our security guard. So if you ever plan on
                            stealing, you&apos;ll have to think twice. We pay her using canned cat foods.
                        </p>
                    </div>

                    <!-- Jeff -->
                    <div class="staff_role">Ice&nbsp;Cream&nbsp;Maker</div>
                    <div class="staff_container">
                        <img class="aboutus-images" src="/images/aboutus/jeff.jpg" alt="Jeff">
                        <p>
                            Our Ice Cream Maker is some guy we grabbed off the street. While he was talking on the
                            phone, we heard that he&apos;s broke and no one&apos;s hiring him. 
                            After that, we offered him the job and he accepted. He&apos;s pretty cool. His name&apos;s Jeff.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FAQ -->
            <div class="aboutus_container">
                <div class="aboutus_title" id="main_faq_title">FAQ</div>
                <div class="aboutus_contents" id="main_faq_contents">

                    <!-- FAQ 1 -->
                    <div class="faq_container">
                        <div class="faq_q" onclick="FAQdropDown(1)">Why are the ice creams called &OpenCurlyQuote;BeenChilling?&CloseCurlyQuote;</div>
                        <div class="faq_a_container" id="faq1">
                            <div id="faq1_wrapper">
                                <div class="faq_a">
                                    BeenChilling is a pun, which is based on the Chinese word &OpenCurlyQuote;冰淇淋&CloseCurlyQuote;.
                                    The word means &OpenCurlyQuote;Ice Cream&CloseCurlyQuote; and it is pronounced as 
                                    &OpenCurlyQuote;Bin Chi Lin&CloseCurlyQuote;, which is very similar to the word 
                                    &OpenCurlyQuote;Been Chilling&CloseCurlyQuote;.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="faq_container">
                        <div class="faq_q" onclick="FAQdropDown(2)">How was BeenChilling founded?</div>
                        <div class="faq_a_container" id="faq2">
                            <div id="faq2_wrapper">
                                <div class="faq_a">
                                    One day our founder, Lee Chee Kai was just chilling under a tree,
                                    when a durian dropped from above and hit his head. When he woke up, he was
                                    already in the hospital. Because of this tragic accident, he decided that he needed
                                    to find a better place to chill. However, such a place didn't exist. This was
                                    when he decided to create BeenChilling so that everyone can have a place to chill in peace.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="faq_container">
                        <div class="faq_q" onclick="FAQdropDown(3)">Is BeenChilling open for Job Applications?</div>
                        <div class="faq_a_container" id="faq3">
                            <div id="faq3_wrapper">
                                <div class="faq_a">
                                    Currently, we don&apos;t accept job applications because for now, we are fully staffed.
                                    Even if one of our staff resigns, we still have some people in mind to recruit because of
                                    their expertise and skills. If one day we don't have anyone else to recruit, job applications
                                    will be announced on our topics page.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 4 -->
                    <div class="faq_container">
                        <div class="faq_q" onclick="FAQdropDown(4)">What&apos;s the secret recipe for BeenChilling?</div>
                        <div class="faq_a_container" id="faq4">
                            <div id="faq4_wrapper">
                                <div class="faq_a">
                                    As the name implies, it is a secret, so we&apos;re not gonna reveal it. However, we can
                                    say with confidence that the BeenChillings are definitely NOT made using human organs.
                                    That would be silly, wouldn&apos;t it? Because it&apos;ll be too expensive!
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="faq_container">
                        <div class="faq_q" onclick="FAQdropDown(5)">Can this website be trusted?</div>
                        <div class="faq_a_container" id="faq5">
                            <div id="faq5_wrapper">
                                <div class="faq_a">
                                    This website contains many security measures to make sure your personal information
                                    doesn't get leaked. Ever since a hacker named &OpenCurlyQuote;Chik Soon Leong&CloseCurlyQuote; hacked
                                    our website and stole our cashier&apos;s phone number (don&apos;t know why),
                                    we have installed many security measures. Unfortunately, the FBI never caught the culprit
                                    after he went into hiding.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contacts -->
            <div class="aboutus_container">
                <div class="aboutus_title" id="contacts_title">Contacts</div>
                <div class="aboutus_contents" id="contacts_contents">

                    <div id="contacts_header">Oh ho, so you want to contact us? You can do so through:</div>

                    <div id="contacts_list">

                        <!-- Phome Number -->
                        <div class="contacts_sections light_brown_line rounded_borders_top">
                            <div class="contacts_category">Phone Number:</div>
                            <div class="contacts_information">015-881-2629</div>
                        </div>

                        <!-- Email -->
                        <div class="contacts_sections brown_line">
                            <div class="contacts_category">Email:</div>
                            <div class="contacts_information">admin@beenchilling.com.my</div>
                        </div>

                        <!-- Twitter -->
                        <div class="contacts_sections light_brown_line">
                            <div class="contacts_category">Twitter:</div>
                            <div class="contacts_information">@beenchilling64</div>
                        </div>

                        <!-- Location -->
                        <div class="contacts_sections brown_line rounded_borders_bottom">
                            <div class="contacts_category">Located At:</div>
                            <div class="contacts_information">Dewan&nbsp;Tunku&nbsp;Abdul&nbsp;Rahman,&nbsp;TARUMT</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Top button -->
        <button id="top" class="fas fa-arrow-up" onclick="topFunction()"></button>
        <br><br>
    </main>

    <!-- Footer -->
    <?php include '../_foot.php'; ?>
    <!-- End of Footer -->
    
    <script src="script.js"></script>
    <script>
        // Run necessary functions
        dropDownHover();
        webPageMusicLoad();
    </script>
</body>
