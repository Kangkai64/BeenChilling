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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/beenchilling.js"></script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KEHC5JXDCZ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-KEHC5JXDCZ');
    </script>
    <!-- End of Google tag (gtag.js) -->
</head>
<div id="splash-screen" style="display: none;">
    <div id="scoop">
        <img src="images/product/Mango.png" alt="Mango.png">
    </div>
    <div id="cone">
        <img src="images/splash_screen/ice_cream_cone.png" alt="ice_cream_cone">
    </div>
    <div id="slogan" class="typewriter">
        <h1>Have you BeenChilling?</h1>
    </div>
</div>

<body>
    <div id="main-content" style="display: none;">
        <!-- Flash message -->
        <div id="info"><?= temp('info') ?></div>
        <div class="nav">
            <div class="logo">
                <a href="/index.php">
                    <img class="logo" src="/images/logo.png" alt="logo">
                </a>
            </div>
            <nav>
                <ul>
                    <?php if ($_user && $_user?->role == 'Admin'): ?>
                        <!-- Admin Navigation Bar -->
                        <li><a class="active_link" href="/page/admin/productlist.php">Product List</a></li>
                        <li><a href="/page/admin/user_list.php">Member List</a></li>
                    <?php else: ?>
                        <!-- Member Navigation Bar -->
                        <li><a class="active_link" href="/index.php">Home</a></li>
                        <li>
                            <div id="dropdown">
                                <a href="/page/member/product.php">Product and Service</a>
                                <div id="dropdown_content">
                                    <a href="/page/member/product.php#Sundae">Sundae</a>
                                    <a href="/page/member/product.php#Dessert">Dessert</a>
                                    <a href="/page/member/product.php#Ice-Cream">Ice-Cream</a>
                                </div>
                            </div>
                        </li>
                        <li><a href="/page/member/topics.php">Topics</a></li>
                        <li><a href="/page/member/reviews.php">Reviews</a></li>
                        <li><a href="/page/member/aboutus.php">About Us</a></li>
                    <?php endif ?>
                </ul>
            </nav>
        </div>
        <div class="user-info-container">
            <div>
                <?= $_user->name ?? 'User' ?><br>
                <?= $_user->role ?? '' ?>
            </div>
            <img src="/images/photo/<?= $_user->photo ?? 'default_avatar.png' ?>" alt="User profile photo">
        </div>
        <!-- Sidebar menu -->
        <div id="sidebar" class="sidebar">
            <button href="javascript:void(0)" class="closebutton" onclick="closeNav()">&times;</button>
            <?php if (!$_user): ?>
                <a href="/page/register.php" class="register-button">Register</a>
                <a href="/page/login.php" class="login-button">Login</a>
            <?php endif ?>
            <?php if ($_user): ?>
                <a href="/page/profile.php" class="profile-button">My Profile</a>
                <a href="/page/password.php" class="password-button">Change Password</a>
                <?php if ($_user->role == 'Member'): ?>
                    <a href="/page/member/shipping_address.php" class="shipping-address-button">My Shipping Address</a>
                <?php endif ?>
                <a href="/page/logout.php" class="logout-button">Logout</a>
            <?php endif ?>
        </div>

        <main>
            <h2 class="topics" id="slogan"><em>Have you BeenChilling?</em></h2>