<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');

session_start();

// Is GET request?
function is_get() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null) {
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null) {
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null) {
    $value = $_REQUEST[$key] ?? $value;
    if (is_array($value)) {
        $value = array_map(function($item) {
            return is_array($item) ? array_map('trim', $item) : trim($item);
        }, $value);
    } else {
        $value = trim($value);
    }
    return $value;
}

// Redirect to URL
function redirect($url = null) {
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    }
    else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// Obtain uploaded file --> cast to object
function get_file($key) {
    $f = $_FILES[$key] ?? null;
    
    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

// Crop, resize and save photo
function save_photo($f, $folder, $width = 200, $height = 200) {
    $photo = uniqid() . '.png';
    
    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;
}

// Is email?
function is_email($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

// Is phone number?
function is_phone_number($value) {
    return preg_match('/^0\d{2}-\d{7,8}$/', $value);
}

// Is correct password pattern?
function is_password($value) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);   
}

// Is money?
function is_money($value) {
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}

// Initialize session cart if it doesn't exist
if (!isset($_SESSION['temp_cart'])) {
    $_SESSION['temp_cart'] = [
        'items' => [],
        'total_items' => 0,
        'total_price' => 0
    ];
}

// Check if user is logged in
function is_logged_in() {
    global $_user;
    return isset($_user) && $_user && $_user->id;
}

// Function to get or create a cart for the current logged-in member
function get_or_create_cart() {
    global $_db, $_user;
    
    if (!is_logged_in()) {
        return null;
    }
    
    // First check for active cart
    $stm = $_db->prepare('SELECT cart_id FROM cart 
                         WHERE member_id = ? AND status = "active" 
                         LIMIT 1');
    $stm->execute([$_user->id]);
    $cart = $stm->fetch(PDO::FETCH_OBJ);
    
    if ($cart) {
        // If the user has a session cart, transfer it to the database
        if (isset($_SESSION['temp_cart']) && !empty($_SESSION['temp_cart']['items'])) {
            transfer_session_cart_to_db($cart->cart_id);
        }
        return $cart->cart_id;
    }
    
    // Check for abandoned cart to recover
    $stm = $_db->prepare('SELECT cart_id FROM cart 
                         WHERE member_id = ? AND status = "abandoned" 
                         ORDER BY updated_at DESC LIMIT 1');
    $stm->execute([$_user->id]);
    $cart = $stm->fetch(PDO::FETCH_OBJ);
    
    if ($cart) {
        // Recover the abandoned cart
        $stm = $_db->prepare('UPDATE cart SET status = "active" WHERE cart_id = ?');
        $stm->execute([$cart->cart_id]);
        
        // If the user has a session cart, transfer it to the database
        if (isset($_SESSION['temp_cart']) && !empty($_SESSION['temp_cart']['items'])) {
            transfer_session_cart_to_db($cart->cart_id);
        }
        
        return $cart->cart_id;
    }
    
    // Create new cart if none found
    $stm = $_db->prepare('INSERT INTO cart (member_id) VALUES (?)');
    $stm->execute([$_user->id]);
    
    $stm = $_db->prepare('SELECT cart_id FROM cart 
                         WHERE member_id = ? ORDER BY created_at DESC LIMIT 1');
    $stm->execute([$_user->id]);
    $cart = $stm->fetch(PDO::FETCH_OBJ);
    
    // If the user has a session cart, transfer it to the database
    if (isset($_SESSION['temp_cart']) && !empty($_SESSION['temp_cart']['items'])) {
        transfer_session_cart_to_db($cart->cart_id);
    }
    
    return $cart->cart_id;
}

// Function to transfer session cart to database cart
function transfer_session_cart_to_db($cart_id) {
    global $_db;
    
    if (empty($_SESSION['temp_cart']['items'])) {
        return;
    }
    
    foreach ($_SESSION['temp_cart']['items'] as $item) {
        // Check if item already exists in cart
        $stm = $_db->prepare('SELECT cart_item_id, quantity FROM cart_item WHERE cart_id = ? AND product_id = ?');
        $stm->execute([$cart_id, $item['product_id']]);
        $db_item = $stm->fetch(PDO::FETCH_OBJ);
        
        if ($db_item) {
            // Update existing item (add quantities)
            $new_quantity = $db_item->quantity + $item['quantity'];
            $stm = $_db->prepare('UPDATE cart_item SET quantity = ? WHERE cart_item_id = ?');
            $stm->execute([$new_quantity, $db_item->cart_item_id]);
        } else {
            // Add new item
            $stm = $_db->prepare('INSERT INTO cart_item (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
            $stm->execute([$cart_id, $item['product_id'], $item['quantity'], $item['price']]);
        }
    }
    
    // Clear session cart after transfer
    $_SESSION['temp_cart'] = [
        'items' => [],
        'total_items' => 0,
        'total_price' => 0
    ];
}

// Function to update session cart item
function update_session_cart_item($product_id, $quantity) {
    global $_db;
    
    // Initialize session cart if it doesn't exist
    if (!isset($_SESSION['temp_cart'])) {
        $_SESSION['temp_cart'] = [
            'items' => [],
            'total_items' => 0,
            'total_price' => 0
        ];
    }
    
    // Get product details
    $stm = $_db->prepare('SELECT ProductID, ProductName, ProductImage, Price FROM product WHERE ProductID = ?');
    $stm->execute([$product_id]);
    $product = $stm->fetch(PDO::FETCH_OBJ);
    
    if (!$product) {
        return false;
    }
    
    // Find item in session cart
    $found = false;
    foreach ($_SESSION['temp_cart']['items'] as $key => $item) {
        if ($item['product_id'] == $product_id) {
            $found = true;
            
            if ($quantity <= 0) {
                // Remove item if quantity is 0
                unset($_SESSION['temp_cart']['items'][$key]);
                $_SESSION['temp_cart']['items'] = array_values($_SESSION['temp_cart']['items']); // Re-index array
            } else {
                // Update quantity
                $_SESSION['temp_cart']['items'][$key]['quantity'] = $quantity;
            }
            
            break;
        }
    }
    
    if (!$found && $quantity > 0) {
        // Add new item
        $_SESSION['temp_cart']['items'][] = [
            'product_id' => $product_id,
            'product_name' => $product->ProductName,
            'product_image' => $product->ProductImage,
            'price' => $product->Price,
            'quantity' => $quantity
        ];
    }
    
    // Recalculate totals
    $total_items = 0;
    $total_price = 0;
    
    foreach ($_SESSION['temp_cart']['items'] as $item) {
        $total_items += $item['quantity'];
        $total_price += $item['price'] * $item['quantity'];
    }
    
    $_SESSION['temp_cart']['total_items'] = $total_items;
    $_SESSION['temp_cart']['total_price'] = $total_price;
    
    return true;
}

function update_cart_item($cart_id, $product_id, $quantity) {
    global $_db;
    
    if (!$cart_id) {
        return false;
    }
    
    // Get product price
    $stm = $_db->prepare('SELECT Price FROM product WHERE ProductID = ?');
    $stm->execute([$product_id]);
    $product = $stm->fetch(PDO::FETCH_OBJ);
    
    if (!$product) {
        return false;
    }
    
    // Check if item already exists in cart
    $stm = $_db->prepare('SELECT cart_item_id FROM cart_item WHERE cart_id = ? AND product_id = ?');
    $stm->execute([$cart_id, $product_id]);
    $item = $stm->fetch(PDO::FETCH_OBJ);
    
    if ($quantity <= 0) {
        // Remove item if quantity is 0
        if ($item) {
            $stm = $_db->prepare('DELETE FROM cart_item WHERE cart_item_id = ?');
            $stm->execute([$item->cart_item_id]);
        }
    } else if ($item) {
        // Update existing item
        $stm = $_db->prepare('UPDATE cart_item SET quantity = ?, price = ? WHERE cart_item_id = ?');
        $stm->execute([$quantity, $product->Price, $item->cart_item_id]);
    } else {
        // Add new item
        $stm = $_db->prepare('INSERT INTO cart_item (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
        $stm->execute([$cart_id, $product_id, $quantity, $product->Price]);
    }
    
    return true;
}

// Function to clear cart
function clear_cart($cart_id = null) {
    global $_db;
    
    if ($cart_id) {
        // Clear database cart
        $stm = $_db->prepare('DELETE FROM cart_item WHERE cart_id = ?');
        $stm->execute([$cart_id]);
    }
    
    // Clear session cart
    $_SESSION['temp_cart'] = [
        'items' => [],
        'total_items' => 0,
        'total_price' => 0
    ];
    
    return true;
}

// Function to get cart items (from db or session)
function get_cart_items($cart_id = null) {
    global $_db;
    
    if ($cart_id) {
        // Get items from database
        $stm = $_db->prepare('
            SELECT ci.*, p.ProductName, p.ProductImage 
            FROM cart_item ci
            JOIN product p ON ci.product_id = p.ProductID
            WHERE ci.cart_id = ?
        ');
        $stm->execute([$cart_id]);
        
        return $stm->fetchAll(PDO::FETCH_OBJ);
    } else {
        // Get items from session
        $items = [];
        
        if (isset($_SESSION['temp_cart']['items'])) {
            foreach ($_SESSION['temp_cart']['items'] as $item) {
                $obj = (object) $item;
                $items[] = $obj;
            }
        }
        
        return $items;
    }
}

// Function to get cart summary (total items and price)
function get_cart_summary($cart_id = null) {
    global $_db;
    
    if ($cart_id) {
        // Get summary from database
        $stm = $_db->prepare('
            SELECT SUM(quantity) as total_items, SUM(quantity * price) as total_price
            FROM cart_item
            WHERE cart_id = ?
        ');
        $stm->execute([$cart_id]);
        
        return $stm->fetch(PDO::FETCH_OBJ);
    } else {
        // Get summary from session
        $summary = (object) [
            'total_items' => $_SESSION['temp_cart']['total_items'] ?? 0,
            'total_price' => $_SESSION['temp_cart']['total_price'] ?? 0
        ];
        
        return $summary;
    }
}

// Get or create wishlist for logged-in user
function get_or_create_wishlist() {
    global $_db, $_user;
    
    if (!is_logged_in()) {
        return [];
    }
    
    // Make sure we're using the member_id value, not the whole user object
    $member_id = is_object($_user) ? $_user->id : $_user;
    
    try {
        // Get all wishlist items for this user
        $stm = $_db->prepare('SELECT product_id, quantity FROM wishlist WHERE member_id = ?');
        $stm->execute([$member_id]);
        
        $wishlist = [];
        while ($item = $stm->fetch(PDO::FETCH_OBJ)) {
            $wishlist[$item->product_id] = $item->quantity;
        }
        
        return $wishlist;
    } catch (PDOException $e) {
        // If there's an error, return an empty wishlist
        return [];
    }
}

// Update wishlist item quantity
function update_wishlist($product_id, $quantity = 0) {
    global $_db, $_user;
    
    // Validate input
    $quantity = (int)$quantity;
    
    // Make sure we're using the member_id value, not the whole user object
    $member_id = is_object($_user) ? $_user->id : $_user;
    
    try {
        if ($quantity > 0) {
            // Check if product exists in wishlist
            $stm = $_db->prepare('SELECT wishlist_id FROM wishlist WHERE member_id = ? AND product_id = ?');
            $stm->execute([$member_id, $product_id]);
            $existing = $stm->fetch(PDO::FETCH_OBJ);
            
            if ($existing) {
                // Update existing wishlist item
                $stm = $_db->prepare('UPDATE wishlist SET quantity = ? WHERE member_id = ? AND product_id = ?');
                return $stm->execute([$quantity, $member_id, $product_id]);
            } else {
                // Insert new wishlist item
                $stm = $_db->prepare('INSERT INTO wishlist (member_id, product_id, quantity) VALUES (?, ?, ?)');
                return $stm->execute([$member_id, $product_id, $quantity]);
            }
        } else {
            // Remove item from wishlist if quantity is 0
            $stm = $_db->prepare('DELETE FROM wishlist WHERE member_id = ? AND product_id = ?');
            return $stm->execute([$member_id, $product_id]);
        }
    } catch (PDOException $e) {
        // Handle database errors
        return false;
    }
}

// Clear wishlist
function clear_wishlist() {
    global $_db, $_user;
    
    // Make sure we're using the member_id value, not the whole user object
    $member_id = is_object($_user) ? $_user->id : $_user;
    
    $stm = $_db->prepare('DELETE FROM wishlist WHERE member_id = ?');
    return $stm->execute([$member_id]);
}

// Function to get wishlist count
function get_wishlist_count($wishlist_id) {
    global $_db;
    
    if (!$wishlist_id) {
        return 0;
    }
    
    $stm = $_db->prepare('SELECT COUNT(*) as count FROM wishlist_item WHERE wishlist_id = ?');
    $stm->execute([$wishlist_id]);
    $result = $stm->fetch(PDO::FETCH_OBJ);
    
    return $result->count ?? 0;
}

// Function to update wishlist item
function update_wishlist_item($wishlist_id, $product_id) {
    global $_db;
    
    if (!$wishlist_id) {
        return false;
    }
    
    // Check if item already exists in wishlist
    $stm = $_db->prepare('SELECT wishlist_item_id FROM wishlist_item WHERE wishlist_id = ? AND product_id = ?');
    $stm->execute([$wishlist_id, $product_id]);
    $item = $stm->fetch(PDO::FETCH_OBJ);
    
    if ($item) {
        // Item already exists, could toggle if desired
        // For now, we'll just ensure it exists
        return true;
    } else {
        // Add new item
        $stm = $_db->prepare('INSERT INTO wishlist_item (wishlist_id, product_id) VALUES (?, ?)');
        $stm->execute([$wishlist_id, $product_id]);
    }
    
    return true;
}

function add_to_cart($product_id, $quantity = 1) {
    global $_db, $_user;
    
    // Validate inputs
    $product_id = $product_id;
    $quantity = (int)$quantity;
    
    if (empty($product_id) || $quantity <= 0) {
        return false;
    }
    
    try {
        // Check if product exists and get its price
        $stm = $_db->prepare('SELECT product_id, price FROM product WHERE product_id = ?');
        $stm->execute([$product_id]);
        $product = $stm->fetch(PDO::FETCH_OBJ);
        if (!$product) {
            return false; // Product doesn't exist
        }
        
        // Get active cart for user or create one if it doesn't exist
        $stm = $_db->prepare('SELECT cart_id FROM cart WHERE member_id = ? AND status = "active" ORDER BY created_at DESC LIMIT 1');
        $stm->execute([$_user]);
        $cart = $stm->fetch(PDO::FETCH_OBJ);
        
        if (!$cart) {
            // Create new cart for user
            $stm = $_db->prepare('INSERT INTO cart (member_id) VALUES (?)');
            $stm->execute([$_user]);
            
            // Get the newly created cart_id (generated by trigger)
            $stm = $_db->prepare('SELECT cart_id FROM cart WHERE member_id = ? ORDER BY created_at DESC LIMIT 1');
            $stm->execute([$_user]);
            $cart = $stm->fetch(PDO::FETCH_OBJ);
        }
        
        // Check if the product is already in the cart
        $stm = $_db->prepare('SELECT cart_item_id, quantity FROM cart_item WHERE cart_id = ? AND product_id = ?');
        $stm->execute([$cart->cart_id, $product_id]);
        $existing_item = $stm->fetch(PDO::FETCH_OBJ);
        
        if ($existing_item) {
            // Update existing cart item
            $new_quantity = $existing_item->quantity + $quantity;
            $stm = $_db->prepare('UPDATE cart_item SET quantity = ? WHERE cart_item_id = ?');
            $result = $stm->execute([$new_quantity, $existing_item->cart_item_id]);
        } else {
            // Insert new cart item
            $stm = $_db->prepare('INSERT INTO cart_item (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
            $result = $stm->execute([$cart->cart_id, $product_id, $quantity, $product->price]);
        }
        
        // Update cart last update time
        $stm = $_db->prepare('UPDATE cart SET updated_at = CURRENT_TIMESTAMP() WHERE cart_id = ?');
        $stm->execute([$cart->cart_id]);
        
        return $result;
    } catch (PDOException $e) {
        // Handle database errors
        return false;
    }
}

// ============================================================================
// HTML Helpers
// ============================================================================

// Encode HTML special characters
function encode($value) {
    return htmlentities($value);
}

// Generate <input type='text'>
function html_text($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='number'>
function html_number($key, $min = '', $max = '', $step = '', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value'
                 min='$min' max='$max' step='$step' $attr>";
}

// Generate <input type='password'>
function html_password($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false) {
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' class = 'search-bar' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '') {
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

// Generate <input type='hidden'>
function html_hidden($key, $attr = '') {
    $value ??= encode($GLOBALS[$key] ?? '');
    echo "<input type='hidden' id='$key' name='$key' value='$value' $attr>";
}

// Generate topics_text
function topics_text($text, $width = '500px') {
    echo "<h2 class='topics' style='width: $width;'>$text</h2>";
}

// Generate product_container
function product_container($id, $product_arr) {
    echo "<h3 class='title' id='$id'>$id</h3>";
    echo "<div class='product-container'>";
    foreach ($product_arr as $product){
        product($product->ProductID, $product->ProductName, $product->Price, $product->ProductImage);
    }
    echo "</div>";
}

// Generate product
function product($id, $name, $price, $image) {
    $formattedPrice = number_format($price, 2);
    
    echo "<div class='product'>";
    echo "<div class='product-background'>";
    echo "<img class='product-images' src='/images/product/$image' alt='$name'>";
    echo "</div>";
    echo "<h3>$name</h3>";
    echo "<h3 class='price'>RM&nbsp;$formattedPrice</h3>";
    echo "<section class='CRUD'>";
    echo "<button class='product-button add-to-cart' data-id='$id' data-name='$name'>Add Cart</button>";
    if($GLOBALS['_user']){
        echo "<button class='product-button add-to-wishlist' data-id='$id' data-name='$name'>Wishlist</button>";
    }
    echo "</section>";
    echo "</div>";
}

// Generate table headers 
function table_headers($fields, $sort, $dir, $href = '') {
    foreach ($fields as $k => $v) {
        $d = 'asc'; 
        $c = '';   

        if ($k == $sort) {
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir;
        }

        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></th>";
    }
}

// Generate <input type='seach'>
function html_search($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='search' id='$key' name='$key' value='$value' class='search-bar' $attr>";
}

// Generate aboutus_container
function aboutus_container($title_class, $title_id, $content_class, $content_id, $title, $content = null) {
    echo "<div class='aboutus_container'>";
    echo "<div class='$title_class' id='$title_id'>$title</div>";
    echo "<div class='$content_class' id='$content_id'>";
    echo $content ?? null;
    echo "</div>";
    echo "</div>";
}

// Genrate staff_container
function staff_container($role, $image, $content) {
    return "<div class='staff_role'>$role</div>
    <div class='staff_container'>
        <img class='aboutus-images' src='/images/aboutus/$image' alt='$image'>
        <p>$content</p>
    </div>";
}

// Generate faq_container
function faq_container($question, $answer) {
    return 
    "<div class='faq_container'>
        <div class='faq_q'>{$question}</div>
        <div class='faq_a_container'>
            <div class='faq_a'>{$answer}</div>
        </div>
    </div>";
}

// Generate contacts header
function contacts_header($text) {
    return 
    "<div id='contacts_header'>{$text}</div>
    <div id='contacts_list'>";
}

// Generate contacts_section
function contacts_section($category, $info) {
    static $count = 0;
    $count++;
    
    $lineColor = ($count % 2 == 0) ? 'brown_line' : 'light_brown_line';
    $roundedTop = ($count == 1) ? 'rounded_borders_top' : '';
    $roundedBottom = ($count == 3) ? 'rounded_borders_bottom' : '';
    
    return 
    "<div class='contacts_sections {$lineColor} {$roundedTop} {$roundedBottom}'>
        <div class='contacts_category'>{$category}:</div>
        <div class='contacts_information'>{$info}</div>
    </div>";
}

// Generate contacts footer to close the list div
function contacts_footer() {
    return "</div>";  // Close contacts_list div
}

// Generate photo view
function photo_view($id, $name, $photo, $details_link, $update_link, $delete_link) {
    echo "<div class='product'>";
    echo "<div class='product-background'>";
    echo "<img class='product-images' src='$photo' alt='$name'>";
    echo "</div>";
    echo "<h3>$id</h3>";
    echo "<h3>$name</h3>";
    echo "<section class='CRUD'>";
    echo "<button class='product-button' data-get='$details_link?id=$id '>Detail</button>";
    echo "<button class='product-button' data-get='$update_link?id=$id '>Update</button>";
    echo "<button class='product-button' data-get='$delete_link?id=$id 'data-confirm>Delete</button>";
    echo "</section>";
    echo "</div>";
}

// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = []; // Array of error messages

// Generate <span class='err'>
function err($key) {
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";
    }
    else {
        echo '<span></span>';
    }
}

// ============================================================================
// Database Setups and Functions
// ============================================================================

// Global PDO object
$_db = new PDO('mysql:dbname=beenchilling', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// Is unique?
function is_unique($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

// ============================================================================
// Security
// ============================================================================

// Global user object
$_user = $_SESSION['user'] ?? null;

// Login user
function login($user, $url = '/') {
    $_SESSION['user'] = $user;
    redirect($url);
}

// Logout user
function logout($url = '/') {
    // Clear session variables and destroy session
    $_SESSION['user'] = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    redirect($url);
}

// Function to mark cart as abandoned when user logs out
function abandon_active_cart($member_id) {
    global $_db;
    
    // Update the cart status to abandoned
    $stm = $_db->prepare('
        UPDATE cart 
        SET status = "abandoned" 
        WHERE member_id = ? AND status = "active"
    ');
    
    return $stm->execute([$member_id]);
}

// Authorization
function auth(...$roles) {
    global $_user;
    if ($_user) {
        if ($roles) {
            if (in_array($_user->role, $roles)) {
                return; // OK
            }
        }
        else {
            return; // OK
        }
    }
    
    redirect('/page/login.php');
}

// ============================================================================
// Global Constants and Variables
// ============================================================================

$_producttype = $_db->query('SELECT TypeID, TypeName FROM producttype')
                    ->fetchAll(PDO::FETCH_KEY_PAIR);

$_role = $_db->query('SELECT DISTINCT role FROM user')
                    ->fetchAll(PDO::FETCH_COLUMN);

// Convert $_role to associative array format for html_select function
$role_options = array();
foreach($_role as $roleName) {
    $role_options[$roleName] = $roleName;
}

$_units = array_combine(range(1, 20), range(1, 20));