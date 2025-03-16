<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
    content="Ice-Cream, Sundae and Dessert Shop">
    <meta name="keywords"
    content="chill, chilling, beenchilling, been chilling, ice-cream, sundae, dessert">
    <meta name="author"
    content="Ho Kang Kai, Lee Yong Kang, Poh Qi Xuan, Kok Xiang Yue, Tung Chee Xun">
    <title><?= $_title ?? "Untitled" ?></title>
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/d743fd0ad4.js" crossorigin="anonymous"></script>
    <script src="/js/beenchilling.js"></script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KEHC5JXDCZ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-KEHC5JXDCZ');
    </script>
<!-- End of Google tag (gtag.js) -->
</head>
<body>
    <header>
        <div class="logo">
            <a href="/index.php">
                <img class="logo" src="/images/logo.png" alt="logo">
            </a>
        </div>
    </header>

    <main>
        <h2 class="topics" id="slogan"><em>Have you BeenChilling?</em></h2>
        
        <nav>
            <ul>
                <!--  Need to active this link to see product list page  -->
                <!-- <li><a href="/page/productlist.php"></a>Product List</li> -->
                <li><a class="active_link" href="/index.php">Home</a></li>
                <li>
                    <div id="dropdown">
                        <a href="/page/product.php">Product and Service</a>
                        <div id="dropdown_content">
                            <a href="/page/product.php#Sundae">Sundae</a>
                            <a href="/page/product.php#Dessert">Dessert</a>
                            <a href="/page/product.php#Ice-Cream">Ice-Cream</a>
                        </div>
                    </div>
                </li>
                <li><a href="/page/topics.php">Topics</a></li>
                <li><a href="/page/reviews.php">Reviews</a></li>
                <li><a href="/page/aboutus.php">About Us</a></li>
            </ul>
        </nav>