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
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <script src="../js/beenchilling.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/d743fd0ad4.js" crossorigin="anonymous"></script>
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
        <img class="logo" data-get="index.php" src="../images/logo.png" alt="logo">
    </header>

    <main>
        <h2 class="topics" id="slogan"><em>Have you BeenChilling?</em></h2>
        
        <nav>
            <ul>
                <li class="active_link" data-get="index.php">Home</li>
                <li>
                    <div id="dropdown">
                        <a data-get="product.php">Product and Service</a>
                        <div id="dropdown_content">
                            <div id="dropdown_wrapper">
                                <a>Sundae</a>
                                <a>Dessert</a>
                                <a>Ice-Cream</a>
                            </div>
                        </div>
                    </div>
                </li>
                <li data-get="topics.php"><a>Topics</a></li>
                <li data-get="reviews.php"><a>Reviews</a></li>
                <li data-get="aboutus.php"><a>About Us</a></li>
            </ul>
        </nav>