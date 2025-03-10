<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');

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
    return is_array($value) ? array_map('trim', $value) : trim($value);
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
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate topics_text
function topics_text($text) {
    echo "<h2 class='topics'>$text</h2>";
}

// Generate menu
function menu($name, $price, $ingredients, $image) {
    $formattedPrice = number_format($price, 2);
    echo "<div class='menu'>";
    echo "<img class='menu' src='/images/product/$image' alt='$name'>";
    echo "<h1>$name RM&nbsp;$formattedPrice</h1>";
    echo "<span>Ingredients</span>";
    echo "<ul>";
    foreach ($ingredients as $ingredient) {
        echo "<li>$ingredient</li>";
    }
    echo "</ul>";
    echo "<button class='cta'>Buy Now</button>";
    echo "</div>";
}

// Generate product_container
function product_container($id, $products = null) {
    echo "<h3 class='title' id='$id'>$id</h3>";
    echo "<div class='product-container'>";
    echo $products ?? null;
    echo "</div>";
}

// Generate product
function product($name, $price, $image) {
    $formattedPrice = number_format($price, 2);
    return "<div class='product'>
    <div class='product-background'>
    <img class='product-images' src='/images/product/$image' alt='$name'>
    </div>
    <h3>$name</h3>
    <h3 class='price'>RM&nbsp;$formattedPrice</h3>
    </div>";
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
    $roundedBottom = ($count == 4) ? 'rounded_borders_bottom' : '';
    
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
// Global Constants and Variables
// ============================================================================

// $_icecream = $_db->query('SELECT IceCreamID, Flavour from icecream')
//                  ->fetchAll(PDO::FETCH_KEY_PAIR);


